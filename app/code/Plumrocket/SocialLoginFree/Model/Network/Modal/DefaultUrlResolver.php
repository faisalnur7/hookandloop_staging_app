<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Modal;

use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;

/**
 * @since 3.2.0
 */
class DefaultUrlResolver implements UrlResolverInterface
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    protected $networkConfig;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

    /**
     * @var string
     */
    private $urlPath;

    /**
     * @var array
     */
    private $urlParams;

    /**
     * @var string
     */
    private $code;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                  $networkConfig
     * @param \Magento\Framework\Url\Helper\Data                                 $urlHelper
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver $modalCallbackUrlResolver
     * @param string                                                             $code
     * @param string                                                             $urlPath
     * @param array                                                              $urlParams
     */
    public function __construct(
        Network $networkConfig,
        UrlHelper $urlHelper,
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        string $code,
        string $urlPath = '',
        array $urlParams = []
    ) {
        $this->networkConfig = $networkConfig;
        $this->urlHelper = $urlHelper;
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->urlPath = $urlPath;
        $this->urlParams = $urlParams;
        $this->code = $code;
    }

    /**
     * Get modal window popup.
     *
     * @return string
     * @throws \Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException
     */
    public function getUrl(): string
    {
        $applicationId = $this->networkConfig->getApplicationId($this->getCode());
        $secret = $this->networkConfig->getApplicationSecretKey($this->getCode());

        if (! $applicationId || ! $secret) {
            throw new NetworkIsNotConfiguredException(
                __(
                    '%1 API credentials are invalid or missing. ' .
                    'Please check the Social Login user guide to make sure the integration is set up correctly.',
                    $this->networkConfig->getTitle($this->getCode())
                )
            );
        }

        $urlParams = [];
        foreach ($this->getUrlParams() as $key => $value) {
            if (strpos($value, '{{') === 0) {
                $value = $this->resolveVariable($value);
            }
            $urlParams[$key] = $value;
        }

        return $this->urlHelper->addRequestParam($this->getUrlPath(), $urlParams);
    }

    /**
     * Get network code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Point for dynamic url params manipulation.
     *
     * @spi
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->urlParams;
    }

    /**
     * Point for dynamic url path manipulation.
     *
     * @spi
     * @return string
     */
    public function getUrlPath(): string
    {
        return $this->urlPath;
    }

    /**
     * Replace variable with real value.
     *
     * @param string $value
     * @return string
     */
    private function resolveVariable(string $value): string
    {
        switch ($value) {
            case '{{APP_ID}}':
                return $this->networkConfig->getApplicationId($this->getCode());
            case '{{REDIRECT_URL}}':
                return $this->modalCallbackUrlResolver->getUrl($this->getCode());
            default:
                return '';
        }
    }
}
