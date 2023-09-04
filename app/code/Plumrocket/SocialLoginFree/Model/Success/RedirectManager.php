<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Success;

use Magento\Cms\Helper\Page as CmsPageHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\DecoderInterface as UrlDecoderInterface;
use Magento\Framework\UrlInterface;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider;

class RedirectManager
{
    public const AFTER_LOGIN = 'login';
    public const AFTER_LOGIN_LINK = 'login_link';
    public const AFTER_REGISTER = 'register';
    public const AFTER_REGISTER_LINK = 'register_link';

    const REFERER_QUERY_PARAM_NAME = 'pslogin_referer';

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var UrlDecoderInterface
     */
    private $urlDecoder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    private $customerUrl;

    /**
     * @var CmsPageHelper
     */
    private $cmsPageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider
     */
    private $lastViewedPageProvider;

    /**
     * RedirectManager constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Helper\Config                         $config
     * @param \Magento\Framework\App\RequestInterface                           $request
     * @param \Magento\Framework\Url\DecoderInterface                           $urlDecoder
     * @param \Magento\Customer\Model\Session                                   $customerSession
     * @param \Magento\Customer\Model\Url                                       $customerUrl
     * @param \Magento\Cms\Helper\Page                                          $cmsPageHelper
     * @param \Magento\Framework\UrlInterface                                   $url
     * @param \Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider $lastViewedPageProvider
     */
    public function __construct(
        Config $config,
        RequestInterface $request,
        UrlDecoderInterface $urlDecoder,
        Session $customerSession,
        CustomerUrl $customerUrl,
        CmsPageHelper $cmsPageHelper,
        UrlInterface $url,
        LastViewedPageProvider $lastViewedPageProvider
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->urlDecoder = $urlDecoder;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->cmsPageHelper = $cmsPageHelper;
        $this->url = $url;
        $this->lastViewedPageProvider = $lastViewedPageProvider;
    }

    /**
     * Get redirect after registration.
     *
     * @param string $redirectTo
     * @return string
     */
    public function getAfterRegisterUrl(string $redirectTo = ''): string
    {
        return $this->getRedirectUrl(self::AFTER_REGISTER, $redirectTo);
    }

    /**
     * Get redirect after login.
     *
     * @param string $customRedirect
     * @return string
     */
    public function getAfterLoginUrl(string $customRedirect = ''): string
    {
        return $this->getRedirectUrl(self::AFTER_LOGIN, $customRedirect);
    }

    /**
     * Get redirect url.
     *
     * @param string $after          login/register
     * @param string $customRedirect force redirect
     * @return string
     */
    public function getRedirectUrl(string $after, string $customRedirect = ''): string
    {
        if ($customRedirect === 'checkout') {
            return $this->url->getUrl('checkout');
        }

        $redirectUrl = '';

        $redirects = $this->getRedirectsConfiguration();

        switch ($redirects[$after]) {

            case '__referer__':
                $links = [];
                if ($referer = $this->lastViewedPageProvider->getUrl()) {
                    $links[] = $referer;
                }
                if ($referer = $this->request->getParam(Url::REFERER_QUERY_PARAM_NAME)) {
                    $links[] = $this->urlDecoder->decode($referer);
                }

                if ($referer = $this->getRefererUrl()) {
                    $links[] = $referer;
                }

                foreach ($links as $url) {
                    // Rebuild referer URL to handle the case when SID was changed
                    $referer = $this->url->getRebuiltUrl($url);

                    if ($this->isUrlInternal($referer)) {
                        $redirectUrl = $referer;
                        break;
                    }
                }

                break;

            case '__custom__':
                $redirectUrl = $redirects["{$after}_link"];
                if (! $this->isUrlInternal($redirectUrl)) {
                    $redirectUrl = $this->url->getBaseUrl() . $redirectUrl;
                }
                break;

            case '__dashboard__':
                $redirectUrl = $this->customerUrl->getDashboardUrl();
                break;

            default:
                if (is_numeric($redirects[$after])) {
                    $redirectUrl = $this->cmsPageHelper->getPageUrl($redirects[$after]);
                }
        }

        if (! $redirectUrl) {
            $redirectUrl = $this->customerUrl->getDashboardUrl();
        }

        return (string) $redirectUrl;
    }

    /**
     * Check if url is internal.
     *
     * @param string $url
     * @return bool
     */
    public function isUrlInternal(string $url): bool
    {
        return stripos($url, 'http') === 0;
    }

    /**
     * Get redirect configs.
     *
     * @return string[]
     */
    public function getRedirectsConfiguration(): array
    {
        return [
            self::AFTER_LOGIN         => $this->config->getRedirectForLogin(),
            self::AFTER_LOGIN_LINK    => $this->config->getRedirectForLoginLink(),
            self::AFTER_REGISTER      => $this->config->getRedirectForRegister(),
            self::AFTER_REGISTER_LINK => $this->config->getRedirectForRegisterLink(),
        ];
    }

    /**
     * @return string
     */
    public function getRefererUrl(): string
    {
        return (string) $this->customerSession->getData(self::REFERER_QUERY_PARAM_NAME);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setRefererUrl(string $url): self
    {
        $this->customerSession->setData(self::REFERER_QUERY_PARAM_NAME, $url);
        return $this;
    }

    /**
     * @return $this
     */
    public function unsetRefererUrl(): self
    {
        $this->customerSession->unsetData(self::REFERER_QUERY_PARAM_NAME);
        return $this;
    }
}
