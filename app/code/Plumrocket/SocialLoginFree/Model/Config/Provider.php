<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Config;

/**
 * @deprecated since 4.0.0
 * @see \Plumrocket\SocialLoginFree\Helper\Config
 */
class Provider
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Provider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Base Helper has got same method
     *
     * Receive magento config value
     *
     * @param string      $path
     * @param string|int  $store
     * @param string|null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }
}
