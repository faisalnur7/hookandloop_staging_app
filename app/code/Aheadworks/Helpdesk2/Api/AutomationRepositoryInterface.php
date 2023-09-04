<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Automation CRUD interface
 * @api
 */
interface AutomationRepositoryInterface
{
    /**
     * Save automation
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation);

    /**
     * Retrieve automation by ID
     *
     * @param int $automationId
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($automationId);

    /**
     * Retrieve automation list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete automation
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation);

    /**
     * Delete automation by ID
     *
     * @param int $automationId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($automationId);
}
