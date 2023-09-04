<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;

/**
 * Class TicketAssignedEventTrigger
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated
 */
class TicketAssignedEventTrigger implements DetectorInterface
{
    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function detect($data)
    {
        /** @var TicketInterface $oldTicket */
        $oldTicket = $data['old_ticket'];
        /** @var TicketInterface $newTicket */
        $newTicket = $data['new_ticket'];
        if ($oldTicket->getAgentId() != $newTicket->getAgentId()) {
            $this->eventManager->dispatch(
                Event::EVENT_NAME_PREFIX . Event::TICKET_ASSIGNED,
                [
                    'ticket' => $newTicket,
                ]
            );
        }
    }
}
