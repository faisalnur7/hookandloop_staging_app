<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Helper\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Plumrocket\Base\Api\ConfigUtilsInterface;

/**
 * @since 4.0.0
 */
class PopupLoginConfig extends AbstractHelper
{

    public const XML_PATH_CUSTOM_LOGIN_DESIGN = 'prpopuplogin/social_login_buttons/login_design';
    public const XML_PATH_CUSTOM_REGISTER_DESIGN = 'prpopuplogin/social_login_buttons/registration_design';

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface $configUtils
     */
    public function __construct(Context $context, ConfigUtilsInterface $configUtils)
    {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Get design for login form.
     *
     * @return string
     */
    public function getCustomLoginDesign(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_CUSTOM_LOGIN_DESIGN);
    }

    /**
     * Get design for registration form.
     *
     * @return string
     */
    public function getCustomRegistrationDesign(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_CUSTOM_REGISTER_DESIGN);
    }
}
