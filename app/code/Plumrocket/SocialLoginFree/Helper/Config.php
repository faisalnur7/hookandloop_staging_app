<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Plumrocket\Base\Api\ConfigUtilsInterface;

class Config extends AbstractHelper
{
    public const SECTION_ID = 'psloginfree';
    public const XML_PATH_REPLACE_TEMPLATE = 'psloginfree/general/replace_templates';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface        $configUtils
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        ConfigUtilsInterface $configUtils
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->configUtils = $configUtils;
    }

    /**
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag('psloginfree/general/enable');
    }

    /**
     * @return bool
     */
    public function isEnabledSubscription(): bool
    {
        return $this->configUtils->isSetFlag('psloginfree/general/enable_subscription');
    }

    /**
     * Check if template replacing is enabled.
     *
     * @return bool
     */
    public function shouldReplaceTemplate(): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_REPLACE_TEMPLATE);
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button::getSorting
     *
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getSortableParams($store = null, $scope = null): array
    {
        $value = $this->configUtils->getConfig('psloginfree/general/sortable', $store, $scope);
        if ($value) {
            parse_str($value, $sortParams);
            return $sortParams;
        }

        return [];
    }

    /**
     * @return bool
     */
    public function createFakeData(): bool
    {
        return $this->configUtils->isSetFlag('psloginfree/general/validate_ignore');
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return bool
     */
    public function isPhotoEnabled($scopeCode = null, $scope = null): bool
    {
        return $this->configUtils->isSetFlag('psloginfree/general/enable_photo', $scopeCode, $scope);
    }

    /**
     * @param null $store
     * @param null $scope
     * @return array
     */
    public function getEnabledPositions($store = null, $scope = null): array
    {
        return $this->configUtils->prepareMultiselectValue(
            (string) $this->configUtils->getConfig('psloginfree/general/enable_for', $store, $scope)
        );
    }

    /**
     * @param string $position
     * @param null   $store
     * @param null   $scope
     * @return bool
     */
    public function isModulePositionEnabled(string $position, $store = null, $scope = null): bool
    {
        return $this->isModuleEnabled() && in_array($position, $this->getEnabledPositions($store, $scope), true);
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Network::isEnabled()
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return bool
     */
    public function isEnabledNetwork(string $type, $scopeCode = null, $scope = null): bool
    {
        return (bool) $this->getConfigByGroup($type, 'enable', $scopeCode, $scope);
    }

    /**
     * Retrieve Application ID
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Network::getApplicationId()
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkApplicationId(string $type, $scopeCode = null, $scope = null): string
    {
        return trim((string)$this->getConfigByGroup($type, 'application_id', $scopeCode, $scope));
    }

    /**
     * Retrieve encoded secret key of application
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Network::getApplicationSecretKey()
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkApplicationSecretKey(string $type, $scopeCode = null, $scope = null): string
    {
        return trim(
            $this->encryptor->decrypt(
                $this->getConfigByGroup($type, 'secret', $scopeCode, $scope)
            )
        );
    }

    /**
     * Retrieve name of icon
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkSmallIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'icon_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve name of icon
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkLoginIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'login_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve name of icon
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkRegisterIconButton(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'register_btn', $scopeCode, $scope);
    }

    /**
     * Retrieve text from button
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button::getLoginBtnText()
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkLoginButtonText(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'login_btn_text', $scopeCode, $scope);
    }

    /**
     * Retrieve text from button
     *
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config\Button::getRegisterBtnText
     *
     * @param string $type
     * @param null   $scopeCode
     * @param null   $scope
     * @return string
     */
    public function getNetworkRegisterButtonText(string $type, $scopeCode = null, $scope = null): string
    {
        return (string) $this->getConfigByGroup($type, 'register_btn_text', $scopeCode, $scope);
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->configUtils->isSetFlag('psloginfree/developer/enable');
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForLogin($scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig('psloginfree/general/redirect_for_login', $scopeCode, $scope);
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForLoginLink($scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(
            'psloginfree/general/redirect_for_login_link',
            $scopeCode,
            $scope
        );
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForRegister($scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig('psloginfree/general/redirect_for_register', $scopeCode, $scope);
    }

    /**
     * @param null $scopeCode
     * @param null $scope
     * @return string
     */
    public function getRedirectForRegisterLink($scopeCode = null, $scope = null): string
    {
        return (string) $this->configUtils->getConfig(
            'psloginfree/general/redirect_for_register_link',
            $scopeCode,
            $scope
        );
    }

    /**
     * Receive magento config value
     * @deprecated since 4.0.0
     * @see \Plumrocket\Base\Api\ConfigUtilsInterface::getConfig
     *
     * @param  string          $group second part of the path, e.g. "general"
     * @param  string          $path third part of the path, e.g. "enabled"
     * @param  string|int|null $scopeCode
     * @param  string|null     $scope
     * @return mixed
     */
    public function getConfigByGroup($group, $path, $scopeCode = null, $scope = null)
    {
        return $this->configUtils->getConfig(
            implode('/', ['psloginfree', $group, $path]),
            $scopeCode,
            $scope
        );
    }
}
