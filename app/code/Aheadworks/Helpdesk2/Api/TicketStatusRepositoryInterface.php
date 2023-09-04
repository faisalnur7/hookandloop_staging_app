<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Ticket status CRUD interface
 *
 * Interface TicketStatusRepositoryInterface
 * @api
 */
interface TicketStatusRepositoryInterface
{
    /**
     * Save ticket status
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface $status
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface $status);

    /**
     * Retrieve ticket status by ID
     *
     * @param int $statusId
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($statusId);

    /**
     * Retrieve ticket status list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketStatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
