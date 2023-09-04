<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Provider;

use Plumrocket\SocialLoginFree\Helper\Config\Network;

class Types implements \Plumrocket\SocialLoginFree\Api\TypesProviderInterface
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var int[]
     */
    private $networkEnabledInfo;

    /**
     * Types constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network $networkConfig
     */
    public function __construct(Network $networkConfig)
    {
        $this->networkConfig = $networkConfig;
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getEnabledList($scopeCode = null, $scope = null)
    {
        return array_keys(array_filter($this->getNetworkStatusInfo($scope, $scopeCode)));
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getDisabledList($scopeCode = null, $scope = null)
    {
        return array_keys(
            array_filter(
                $this->getNetworkStatusInfo($scope, $scopeCode),
                static function ($status) {
                    return !$status;
                }
            )
        );
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    public function getAll($scopeCode = null, $scope = null)
    {
        return array_keys($this->getNetworkStatusInfo($scope, $scopeCode));
    }

    /**
     * @param null $scope
     * @param null $scopeCode
     * @return array
     */
    private function getNetworkStatusInfo($scope = null, $scopeCode = null): array
    {
        if (null === $this->networkEnabledInfo) {
            $this->networkEnabledInfo = [
                'facebook' => (int) $this->networkConfig->isEnabled('facebook', $scope, $scopeCode),
                'twitter' => (int) $this->networkConfig->isEnabled('twitter', $scope, $scopeCode),
            ];
        }
        return $this->networkEnabledInfo;
    }
}
