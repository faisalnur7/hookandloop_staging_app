<?php
namespace Aheadworks\Helpdesk2\Model\Department;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Ticket\Search\Builder as SearchBuilder;

/**
 * Class TicketChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class TicketChecker
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(
        SearchBuilder $searchBuilder
    ) {
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * Check if department has tickets assigned to it
     *
     * @param int $departmentId
     * @return bool
     * @throws LocalizedException
     */
    public function hasTicketsAssigned($departmentId)
    {
        $searchCriteriaBuilder = $this->searchBuilder->getSearchCriteriaBuilder();
        $this->searchBuilder->addDepartmentFilter($departmentId);
        $searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(1);
        $tickets = $this->searchBuilder->searchTickets();

        return count($tickets) > 0;
    }
}
