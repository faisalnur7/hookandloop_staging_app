<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api;

use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;

/**
 * @since 4.0.0
 */
interface NetworkButtonProviderInterface
{
    /**
     * Get network button.
     *
     * @param string $networkCode
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $networkCode): ButtonInterface;

    /**
     * Check if there is at least one enabled integration.
     *
     * @return bool
     */
    public function hasButtons(): bool;

    /**
     * Get default buttons list
     *
     * Only enabled buttons and sorted by visibility
     *
     * @param int|null $storeId
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     */
    public function getDefaultList($storeId = null): array;
}
