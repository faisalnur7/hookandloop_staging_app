<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Service;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketHistoryManagementInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class TicketHistoryService
 */
class TicketHistoryService implements TicketHistoryManagementInterface
{
    /**
     * TicketHistoryService constructor.
     *
     * @param SearchBuilder $searchBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        private SearchBuilder $searchBuilder,
        private SortOrderBuilder $sortOrderBuilder
    ) {
    }

    /**
     * Get last open tickets for customer
     *
     * @param int $customerId
     * @param null|int $ticketListSize
     * @return TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastOpenTicketsForCustomer(int $customerId, ?int $ticketListSize = null): array
    {
        $searchCriteriaBuilder = $this->searchBuilder->getSearchCriteriaBuilder();
        $sortOrder = $this->sortOrderBuilder
            ->setField(TicketInterface::CREATED_AT)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        $searchCriteriaBuilder->addFilter(
            TicketInterface::STATUS_ID,
            [TicketStatus::NEW, TicketStatus::OPEN, TicketStatus::WAITING],
            'in'
        )->addFilter(
            TicketInterface::CUSTOMER_ID,
            [$customerId],
            'in'
        )->addSortOrder($sortOrder);

        if ($ticketListSize) {
            $searchCriteriaBuilder->setPageSize($ticketListSize);
        }
        return $this->searchBuilder->searchTickets();
    }
}
