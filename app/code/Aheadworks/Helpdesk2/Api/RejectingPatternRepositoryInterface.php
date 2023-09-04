<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Rejection pattern CRUD interface
 * @api
 */
interface RejectingPatternRepositoryInterface
{
    /**
     * Save pattern
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface $pattern
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface $pattern);

    /**
     * Retrieve pattern by ID
     *
     * @param int $patternId
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($patternId);

    /**
     * Retrieve pattern list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectingPatternSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete pattern
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface $pattern
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface $pattern);

    /**
     * Delete pattern by ID
     *
     * @param int $patternId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($patternId);
}
