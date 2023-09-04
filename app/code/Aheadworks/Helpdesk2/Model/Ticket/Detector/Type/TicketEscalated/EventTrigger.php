<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketEscalated;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;

/**
 * Class EventTrigger
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketEscalated
 */
class EventTrigger implements DetectorInterface
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
        $this->eventManager->dispatch(Event::EVENT_NAME_PREFIX . Event::TICKET_ESCALATED, $data);
    }
}
