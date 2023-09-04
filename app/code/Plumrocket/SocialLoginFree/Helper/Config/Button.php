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
use Plumrocket\SocialLoginFree\Model\OptionSource\ButtonDesign;

/**
 * @since 4.0.0
 */
class Button extends AbstractHelper
{

    public const XML_PATH_ENABLE_FOR = 'psloginfree/general/enable_for';
    public const XML_PATH_BUTTON_SORT = 'psloginfree/general/sortable';

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface $configUtils
     */
    public function __construct(
        Context $context,
        ConfigUtilsInterface $configUtils
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Check in module buttons can be shown on
     *
     * @param string $position
     * @return bool
     */
    public function canDisplayOn(string $position): bool
    {
        return in_array($position, $this->getDisplayOn(), true);
    }

    /**
     * Get list of enabled positions.
     *
     * @return string[]
     */
    public function getDisplayOn(): array
    {
        $value = (string) $this->configUtils->getStoreConfig(self::XML_PATH_ENABLE_FOR);
        return $this->configUtils->prepareMultiselectValue($value);
    }

    /**
     * Get button sorting.
     *
     * Response examples:
     *
     * 1. Default
     * [
     *   visible => ['facebook', 'twitter']
     * ]
     * 2. Sorting is not set
     * []
     *
     * @param null|string|int $store
     * @return array
     */
    public function getSorting($store = null): array
    {
        // Example of config: visible[]=facebook&visible[]=twitter
        $value = $this->configUtils->getStoreConfig(self::XML_PATH_BUTTON_SORT, $store);
        if ($value) {
            parse_str($value, $sortParams);
            return $sortParams;
        }
        return [];
    }

    /**
     * Get button text for login form.
     *
     * @param string $networkCode
     * @param $scopeCode
     * @param $scope
     * @return string
     */
    public function getLoginBtnText(string $networkCode, $scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(
            "psloginfree/{$networkCode}/login_btn_text",
            $scopeCode,
            $scope
        );
    }

    /**
     * Get button text for register form.
     *
     * @param string $networkCode
     * @param $scopeCode
     * @param $scope
     * @return string
     */
    public function getRegisterBtnText(string $networkCode, $scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(
            "psloginfree/{$networkCode}/register_btn_text",
            $scopeCode,
            $scope
        );
    }

    /**
     * Get button icon for register form.
     *
     * @param $scopeCode
     * @param $scope
     * @return string
     */
    public function getDesign($scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(
            "psloginfree/buttons/design",
            $scopeCode,
            $scope
        ) ?: ButtonDesign::TYPE_DEFAULT;
    }
}
