<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\NewMessage;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;

/**
 * Class EventTrigger
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\NewMessage
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
        /** @var MessageInterface $message */
        $message = $data['message'];
        $eventData = [
            'ticket' => $data['ticket'],
            'message' => $message
        ];
        switch ($message->getType()) {
            case MessageType::CUSTOMER:
                $this->eventManager->dispatch(
                    Event::EVENT_NAME_PREFIX . Event::NEW_REPLY_FROM_CUSTOMER,
                    $eventData
                );
                break;
            case MessageType::ADMIN:
                $this->eventManager->dispatch(
                    Event::EVENT_NAME_PREFIX . Event::NEW_REPLY_FROM_AGENT,
                    $eventData
                );
                break;
        }
    }
}
