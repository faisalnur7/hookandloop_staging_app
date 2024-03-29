<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api;

use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;

/**
 * @since 3.2.0
 */
interface NetworkConnectorInterface
{
    /**
     * Get data from network.
     *
     * @param array $networkResponse
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Plumrocket\SocialLoginPro\Exception\UserAccessDeniedException If User denied access for the application.
     */
    public function getNetworkAccount(array $networkResponse): NetworkAccountInterface;
}
