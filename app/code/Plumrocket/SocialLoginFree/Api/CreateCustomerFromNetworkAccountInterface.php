<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;

/**
 * @since 3.8.0
 */
interface CreateCustomerFromNetworkAccountInterface
{
    /**
     * Create customer.
     *
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface $networkAccount
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InvalidArgumentException
     */
    public function execute(NetworkAccountInterface $networkAccount): CustomerInterface;
}
