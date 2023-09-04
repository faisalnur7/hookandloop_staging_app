<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api\Data;

/**
 * @since 3.2.0
 */
interface NetworkAccountInterface
{

    /**
     * Get network code,
     *
     * @return string
     */
    public function getNetworkCode(): string;

    /**
     * Get network identifier.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get photo url.
     *
     * @return string
     */
    public function getPhotoUrl(): string;

    /**
     * Retrieve only customer information that can be saved by magento model.
     *
     * @return string[]
     */
    public function getCustomerData(): array;

    /**
     * Get additional data.
     *
     * Used for storing network user profile url and so on.
     *
     * @return array
     */
    public function getAdditionalData(): array;

    /**
     * Set additional data.
     *
     * @param array $additionalData
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface
     */
    public function setAdditionalData(array $additionalData): NetworkAccountInterface;

    /**
     * Add additional data.
     *
     * @param string $key
     * @param mixed  $value
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface
     */
    public function addAdditionalData(string $key, $value): NetworkAccountInterface;

    /**
     * Get network account data.
     *
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = []): array;
}
