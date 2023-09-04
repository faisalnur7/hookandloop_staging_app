<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api\Data;

/**
 * @since 4.0.0
 */
interface NetworkAccountLinkInterface
{

    /**
     * Get network code,
     *
     * @return string
     */
    public function getNetworkCode(): string;

    /**
     * Get title of the network.
     *
     * @return string
     */
    public function getNetworkTitle(): string;

    /**
     * Get network user identifier.
     *
     * @return string
     */
    public function getNetworkUserId(): string;

    /**
     * Get magento customer id.
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Get additional data.
     *
     * Used for storing network user profile url and so on.
     *
     * @return array
     */
    public function getAdditionalData(): array;

    /**
     * Set network code,
     *
     * @param string $networkCode
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function setNetworkCode(string $networkCode): NetworkAccountLinkInterface;

    /**
     * Set network user identifier.
     *
     * @param string $networkId
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function setNetworkUserId(string $networkId): NetworkAccountLinkInterface;

    /**
     * Set magento customer id.
     *
     * @param int $customerId
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function setCustomerId(int $customerId): NetworkAccountLinkInterface;

    /**
     * Set additional data.
     *
     * @param array $additionalData
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function setAdditionalData(array $additionalData): NetworkAccountLinkInterface;

    /**
     * Add additional data.
     *
     * @param string $key
     * @param mixed  $value
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function addAdditionalData(string $key, $value): NetworkAccountLinkInterface;
}
