<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Plumrocket\SocialLoginFree\Helper\Config;

/**
 * @since 4.0.0
 */
class SharePopup
{
    public const SHOW_POPUP_COOKIE_NAME = 'pslogin_show_popup';
    public const COOKIE_DURATION = 600;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory
     */
    private $publicCookieMetadataFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\SharePopup
     */
    private $sharePopupConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface             $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory
     * @param \Plumrocket\SocialLoginFree\Helper\Config\SharePopup         $sharePopupConfig
     * @param \Plumrocket\SocialLoginFree\Helper\Config                    $config
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory $publicCookieMetadataFactory,
        \Plumrocket\SocialLoginFree\Helper\Config\SharePopup $sharePopupConfig,
        Config $config
    ) {

        $this->cookieManager = $cookieManager;
        $this->publicCookieMetadataFactory = $publicCookieMetadataFactory;
        $this->sharePopupConfig = $sharePopupConfig;
        $this->config = $config;
    }

    /**
     * Set cookie to show share popup.
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function show(): void
    {
        if (! $this->config->isModuleEnabled() || ! $this->sharePopupConfig->isEnabled()) {
            return;
        }

        $publicCookieMetadata = $this->publicCookieMetadataFactory->create(['metadata' => []]);
        $publicCookieMetadata
            ->setDuration(self::COOKIE_DURATION)
            ->setPath('/');

        $this->cookieManager->setPublicCookie(self::SHOW_POPUP_COOKIE_NAME, 1, $publicCookieMetadata);
    }
}
