<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api;

/**
 * @since 4.0.0
 */
interface CustomerNetworksManagerInterface
{
    /**
     * Get codes of linked social networks.
     *
     * @param int $customerId
     * @return string[]
     */
    public function getLinkedTypesByCustomerId(int $customerId): array;

    /**
     * Get account links of linked social networks.
     *
     * @param int $customerId
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface[]
     */
    public function getLinkedNetworksByCustomerId(int $customerId): array;

    /**
     * Get codes of linked social networks.
     *
     * @return string[]
     */
    public function getLinkedTypesForCurrentCustomer(): array;

    /**
     * Get account links of linked social networks.
     *
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface[]
     */
    public function getLinkedNetworksForCurrentCustomer(): array;

    /**
     * Check if social account is already linked to any account on store.
     *
     * @param string $networkCode
     * @param string $userId
     * @return bool
     */
    public function isNetworkAlreadyLinked(string $networkCode, string $userId): bool;

    /**
     * Get customer id by social account id.
     *
     * @param string $networkCode
     * @param string $userId
     * @return int   customer id or 0
     */
    public function getCustomerIdByNetwork(string $networkCode, string $userId): int;

    /**
     * Link network account to magento customer.
     *
     * @param string      $type
     * @param string      $userId
     * @param int         $customerId
     * @param string|null $customerPhoto
     * @param array|null  $additionalData
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function linkNetworkToCustomer(
        string $type,
        string $userId,
        int $customerId,
        ?string $customerPhoto = null,
        ?array $additionalData = null
    ): void;

    /**
     * Remove account link.
     *
     * @param int $customerId
     * @param int $accountLinkId
     * @return bool
     */
    public function unlinkNetworkFromCustomer(int $customerId, int $accountLinkId): bool;
}
