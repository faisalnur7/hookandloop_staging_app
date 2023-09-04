<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated;

use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as StatusSource;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated
 */
class Status implements DetectorInterface
{
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var StatusSource
     */
    private $statusSource;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     * @param StatusSource $statusSource
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory,
        StatusSource $statusSource,
        EventManagerInterface $eventManager
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->statusSource = $statusSource;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     *
     * @param $data
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function detect($data)
    {
        /** @var TicketInterface $oldTicket */
        $oldTicket = $data['old_ticket'];
        /** @var TicketInterface $newTicket */
        $newTicket = $data['new_ticket'];

        if ($oldTicket->getStatusId() != $newTicket->getStatusId()) {
            $message = $this->messageFactory->createForDetector($newTicket);
            $message
                ->setStatusId($newTicket->getStatusId())
                ->setTicketId($newTicket->getEntityId())
                ->setContent($this->getContent($oldTicket, $newTicket));
            $this->messageRepository->save($message);
            $this->eventManager->dispatch(
                Event::EVENT_NAME_PREFIX . Event::TICKET_STATUS_CHANGE,
                [
                    'ticket' => $newTicket
                ]
            );
        }
    }

    /**
     * Create message content
     *
     * @param TicketInterface $oldTicket
     * @param TicketInterface $newTicket
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getContent($oldTicket, $newTicket)
    {
        $from = $this->statusSource->getOptionById($oldTicket->getStatusId());
        $to = $this->statusSource->getOptionById($newTicket->getStatusId());

        return __('%1 > <b>%2</b>', [$from['label'], $to['label']]);
    }
}
