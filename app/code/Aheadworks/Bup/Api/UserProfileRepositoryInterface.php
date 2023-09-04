<?php
namespace Aheadworks\Bup\Api;

/**
 * Interface UserProfileRepositoryInterface
 * @api
 */
interface UserProfileRepositoryInterface
{
    /**
     * Save user profile
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileInterface $userProfile
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Bup\Api\Data\UserProfileInterface $userProfile);

    /**
     * Retrieve user profile by ID
     *
     * @param int $id
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Retrieve user profile list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
