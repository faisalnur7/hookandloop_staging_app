<?php
namespace Aheadworks\Bup\Api;

/**
 * Interface UserProfileMetadataRepositoryInterface
 * @api
 */
interface UserProfileMetadataRepositoryInterface
{
    /**
     * Retrieve user profile metadata by ID
     *
     * @param int $id
     * @param string $area
     * @return \Aheadworks\Bup\Api\Data\UserProfileMetadataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id, $area = \Aheadworks\Bup\Model\Source\UserProfile\Area::STOREFRONT);

    /**
     * Retrieve user profile metadata list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $area
     * @return \Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $area = \Aheadworks\Bup\Model\Source\UserProfile\Area::STOREFRONT
    );
}
