<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface;

/**
 * @since 4.0.0
 */
interface AccountLinkRepositoryInterface
{
    /**
     * Save linked account.
     *
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface $accountLink
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     */
    public function save(NetworkAccountLinkInterface $accountLink): NetworkAccountLinkInterface;

    /**
     * Get linked account list.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Plumrocket\SocialLoginFree\Api\Data\AccountLinkResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Get linked account by its id.
     *
     * @param int  $accountLinkId
     * @param bool $forceLoad
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $accountLinkId, bool $forceLoad = false): NetworkAccountLinkInterface;

    /**
     * Delete account link.
     *
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface $accountLink
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(NetworkAccountLinkInterface $accountLink): bool;
}
