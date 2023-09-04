<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Quick Response CRUD interface
 * @api
 */
interface QuickResponseRepositoryInterface
{
    /**
     * Save quick response
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface $quickResponse
     * @return \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface $quickResponse);

    /**
     * Retrieve quick response by ID
     *
     * @param int $quickResponseId
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quickResponseId, $storeId = null);

    /**
     * Retrieve quick response matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk2\Api\Data\QuickResponseSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Delete quick response
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface $quickResponse
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface $quickResponse);

    /**
     * Delete quick response by ID
     *
     * @param int $quickResponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($quickResponseId);
}
