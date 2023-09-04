<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated;

use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class InternalNote
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector\Type\TicketUpdated
 */
class InternalNote implements DetectorInterface
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
     * @param MessageRepositoryInterface $messageRepository
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        MessageFactory $messageFactory
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function detect($data)
    {
        /** @var TicketInterface $oldTicket */
        $oldTicket = $data['old_ticket'];
        /** @var TicketInterface $newTicket */
        $newTicket = $data['new_ticket'];

        if ($newTicket->getInternalNote() && $oldTicket->getInternalNote() != $newTicket->getInternalNote()) {
            $message = $this->messageFactory->createInternalNote();
            $message
                ->setTicketId($newTicket->getEntityId())
                ->setContent($newTicket->getInternalNote());
            $this->messageRepository->save($message);
            $message = $this->messageFactory->createForDetector($newTicket);
            $message
                ->setTicketId($newTicket->getEntityId())
                ->setContent(sprintf('<b>%s</b>:</br>%s', __('Note is added'), $newTicket->getInternalNote()));
            $this->messageRepository->save($message);
        }
    }
}
