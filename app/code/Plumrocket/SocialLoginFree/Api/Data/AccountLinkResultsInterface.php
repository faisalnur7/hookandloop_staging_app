<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @since 4.0.0
 */
interface AccountLinkResultsInterface extends SearchResultsInterface
{
    /**
     * Get link account list.
     *
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface[]
     */
    public function getItems(): array;

    /**
     * Set link account list.
     *
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
