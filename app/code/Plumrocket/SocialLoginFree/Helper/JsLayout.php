<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @since 4.0.0
 */
class JsLayout extends AbstractHelper
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Plumrocket\SocialLoginFree\Helper\Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * Get component for checkout authentication.
     *
     * Replace default component to render buttons.
     *
     * @return string
     */
    public function getCheckoutAuthenticationComponent(): string
    {
        if ($this->config->isModuleEnabled() && $this->config->shouldReplaceTemplate()) {
            return 'Plumrocket_SocialLoginFree/js/view/checkout/authentication';
        }
        return 'Magento_Checkout/js/view/authentication';
    }

    /**
     * Get component for checkout email.
     *
     * Replace default component to render buttons.
     *
     * @return string
     */
    public function getCheckoutEmailComponent(): string
    {
        if ($this->config->isModuleEnabled() && $this->config->shouldReplaceTemplate()) {
            return 'Plumrocket_SocialLoginFree/js/view/checkout/form/element/email';
        }
        return 'Magento_Checkout/js/view/form/element/email';
    }

    /**
     * Get component for customer authentication popup.
     *
     * Replace default component to render buttons.
     *
     * @return string
     */
    public function getCustomerAuthenticationPopupComponent(): string
    {
        if ($this->config->isModuleEnabled() && $this->config->shouldReplaceTemplate()) {
            return 'Plumrocket_SocialLoginFree/js/view/customer/authentication-popup';
        }
        return 'Magento_Customer/js/view/authentication-popup';
    }
}
