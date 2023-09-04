<?php
namespace Aheadworks\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Tag Cloud Item repository interface
 * @api
 */
interface TagCloudItemRepositoryInterface
{
    /**
     * Retrieve tag cloud item
     *
     * @param int $tagId
     * @param int $storeId
     * @param string[] $postStatus
     * @return \Aheadworks\Blog\Api\Data\TagCloudItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tagId, $storeId, $postStatus = []);

    /**
     * Retrieve tags cloud items matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $storeId
     * @return \Aheadworks\Blog\Api\Data\TagCloudItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId);
}
