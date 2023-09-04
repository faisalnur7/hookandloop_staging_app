<?php
namespace Aheadworks\Helpdesk2\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Tag CRUD interface
 * @api
 */
interface TagRepositoryInterface
{
    /**
     * Save tag
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface $tag
     * @return \Aheadworks\Helpdesk2\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\TagInterface $tag);

    /**
     * Retrieve tag
     *
     * @param int $tagId
     * @return \Aheadworks\Helpdesk2\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tagId);

    /**
     * Retrieve tags matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete tag
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface $tag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\TagInterface $tag);

    /**
     * Delete tag by ID
     *
     * @param int $tagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tagId);
}
