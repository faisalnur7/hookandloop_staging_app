<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Backend\Model\Session as BackendSession;

/**
 * Class BackendUserHistory
 */
class BackendUserHistory
{
    /**
     * BackendUserHistory constructor.
     *
     * @param BackendSession $backendSession
     */
    public function __construct(
        private BackendSession $backendSession
    ) {
    }


    /**
     * Get admin history tickets id
     *
     * @param int|null $ticketListSize
     * @return array
     */
    public function getAdminHistoryTicketsId(?int $ticketListSize = null): array
    {
        $ticketList = $this->backendSession->getAdminHistoryTickets() ?: [];
        return $ticketListSize ? array_slice($ticketList, $ticketListSize * (-1)) : $ticketList;
    }

    /**
     * Add ticket id to admin history
     *
     * @param int $ticketId
     * @return void
     */
    public function addTicketIdToAdminHistory(int $ticketId): void
    {
        $historyTickets = $this->backendSession->getAdminHistoryTickets() ?: [];
        $historyTickets[] = $ticketId;
        $historyTickets = array_unique($historyTickets);
        $this->backendSession->setAdminHistoryTickets($historyTickets);
    }
}
