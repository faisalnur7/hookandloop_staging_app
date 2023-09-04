<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\BackendUserHistory;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\RequestDataProvider;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\TicketInfoInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketHistoryManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PrepareInfo
 */
class PrepareInfo implements CommandInterface
{
    /**
     * PrepareInfo constructor.
     *
     * @param TicketHistoryManagementInterface $ticketHistoryManagement
     * @param RequestDataProvider $requestDataProvider
     * @param TicketRepositoryInterface $ticketRepository
     * @param BackendUserHistory $backendUserHistory
     */
    public function __construct(
        private TicketHistoryManagementInterface $ticketHistoryManagement,
        private RequestDataProvider $requestDataProvider,
        private TicketRepositoryInterface $ticketRepository,
        private BackendUserHistory $backendUserHistory
    ) {
    }

    /**
     * Execute command
     *
     * @param array $ticketData
     * @return array
     * @throws LocalizedException
     */
    public function execute($ticketData)
    {
        $result = [];
        $ticketId = (int)$ticketData[TicketInterface::ENTITY_ID];
        $ticket = $this->ticketRepository->getById($ticketId);

        $customerTickets = $this->ticketHistoryManagement->getLastOpenTicketsForCustomer(
            (int)$ticket->getCustomerId(),
            TicketInfoInterface::CUSTOMER_HISTORY_TICKETS_DISPLAY_COUNT
        );

        $result['main_ticket'] = $this->requestDataProvider->getPreparedTicketInfo($ticket);

        foreach ($customerTickets as $customerTicket) {
            if ((int)$customerTicket->getEntityId() === (int)$ticketId) {
                continue;
            }
            $result['customer_tickets'][] = $this->requestDataProvider->getPreparedTicketInfo($customerTicket);
        }

        $historyTicketIds = $this->backendUserHistory->getAdminHistoryTicketsId(
            TicketInfoInterface::ADMIN_HISTORY_TICKETS_DISPLAY_COUNT
        );
        foreach ($historyTicketIds as $historyTicketId) {
            if ((int)$historyTicketId === (int)$ticketId) {
                continue;
            }
            try {
                $ticket = $this->ticketRepository->getById($historyTicketId);
                $result['history_tickets'][] = $this->requestDataProvider->getPreparedTicketInfo($ticket);
            } catch (NoSuchEntityException $ex) {
                continue;
            }
        }

        return $result;
    }
}
