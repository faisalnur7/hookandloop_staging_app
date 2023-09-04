<?php
namespace Aheadworks\Helpdesk2\Observer\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Helpdesk2\Model\Automation\Event\TicketEscalatedHandler as EventHandler;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;

/**
 * Class TicketEscalatedEventObserver
 *
 * @package Aheadworks\Helpdesk2\Observer\Ticket
 */
class TicketEscalatedEventObserver implements ObserverInterface
{
    /**
     * @var EventHandler
     */
    private $eventHandler;

    /**
     * @var EventDataInterfaceFactory
     */
    private $eventDataFactory;

    /**
     * @param EventHandler $eventHandler
     * @param EventDataInterfaceFactory $eventDataFactory
     */
    public function __construct(
        EventHandler $eventHandler,
        EventDataInterfaceFactory $eventDataFactory
    ) {
        $this->eventHandler = $eventHandler;
        $this->eventDataFactory = $eventDataFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var EventDataInterface $eventData */
        $eventData = $this->eventDataFactory->create();
        $eventData
            ->setEventName(str_replace(Event::EVENT_NAME_PREFIX, '', $observer->getEvent()->getName()))
            ->setTicket($observer->getData('ticket'))
            ->setEscalationMessage($observer->getData('escalation-message'));

        $this->eventHandler->trigger($eventData);
    }
}
