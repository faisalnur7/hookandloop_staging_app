<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class Close
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Close implements CommandInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @param TicketManagementInterface $ticketManagement
     */
    public function __construct(
        TicketManagementInterface $ticketManagement
    ) {
        $this->ticketManagement = $ticketManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['ticket'])) {
            throw new \InvalidArgumentException('Ticket is required to be closed');
        }

        /** @var TicketInterface $ticket */
        $ticket = $data['ticket'];
        $ticket->setStatusId(TicketStatus::CLOSED);
        $this->ticketManagement->updateTicket($ticket);

        return $ticket;
    }
}
