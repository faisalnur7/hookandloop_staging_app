<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Priority;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\TicketPriorityRepositoryInterface;

/**
 * Class Provider
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Priority
 */
class Provider
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var TicketPriorityRepositoryInterface
     */
    private $ticketPriorityRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectConverter $dataObjectConverter
     * @param TicketPriorityRepositoryInterface $ticketPriorityRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectConverter $dataObjectConverter,
        TicketPriorityRepositoryInterface $ticketPriorityRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->ticketPriorityRepository = $ticketPriorityRepository;
    }

    /**
     * Get status list as options
     *
     * @return array
     * @throws LocalizedException
     */
    public function getListAsOptions()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $priorityList = $this->ticketPriorityRepository->getList($searchCriteria)->getItems();
        $id = function (TicketPriorityInterface $priority) {
            return (string)$priority->getId();
        };
        $label = function (TicketPriorityInterface $priority) {
            return __($priority->getLabel());
        };
        return $this->dataObjectConverter->toOptionArray(
            $priorityList,
            $id,
            $label
        );
    }
}
