<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Api\MessageRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\MessageFactory;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class MergeHandler
 */
class MergeHandler
{
    /**
     * @var array
     */
    private $ccRecipientsToMerge = [];

    /**
     * @var array
     */
    private $tagsToMerge = [];

    /**
     * @var array
     */
    private $notesToMerge = [];

    /**
     * MergeHandler constructor.
     *
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageFactory $messageFactory
     * @param MessageRepositoryInterface $messageRepository
     * @param TicketRepositoryInterface $ticketRepository
     * @param TicketManagementInterface $ticketManagement
     * @param ProcessorInterface $replyTicketDataProcessor
     * @param CommandInterface $replyTicketCommand
     */
    public function __construct(
        private MessageCollectionFactory $messageCollectionFactory,
        private MessageFactory $messageFactory,
        private MessageRepositoryInterface $messageRepository,
        private TicketRepositoryInterface $ticketRepository,
        private TicketManagementInterface $ticketManagement,
        private ProcessorInterface $replyTicketDataProcessor,
        private CommandInterface $replyTicketCommand
    ) {
    }

    /**
     * Merge tickets
     *
     * @param TicketInterface $mainTicket
     * @param TicketInterface[] $ticketsToMerge
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function merge(TicketInterface $mainTicket, array $ticketsToMerge): bool
    {
        $this->collectMergeData($mainTicket, $ticketsToMerge);

        $mainTicket = $this->mergeTags($mainTicket);
        $mainTicket = $this->mergeCcRecipients($mainTicket);
        $this->ticketManagement->updateTicket($mainTicket);

        $this->mergeNotes($mainTicket);
        $this->processTicketComment($mainTicket);

        foreach ($ticketsToMerge as $ticketToMerge) {
            $this->processTicketComment($ticketToMerge);
            $this->closeTicket($ticketToMerge);
        }

        return true;
    }

    /**
     * Process ticket comment
     *
     * @param TicketInterface $ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processTicketComment(TicketInterface $ticket): void
    {
        $mergeComment = $ticket->getData('merge_comment');
        if ($mergeComment && strlen($mergeComment)) {
            $isRequestedSeeMergeComment = $ticket->getData('is_requested_see_merge_comment');
            if ($isRequestedSeeMergeComment) {
                $this->createReply($ticket);
            } else {
                $this->createNote($ticket);
            }
        }
    }

    /**
     * Create reply for ticket
     *
     * @param TicketInterface $ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function createReply(TicketInterface $ticket): void
    {
        $data = $this->replyTicketDataProcessor->prepareEntityData([
            'entity_id' => $ticket->getEntityId(),
            'status_id' => $ticket->getStatusId(),
            'content' => $ticket->getData('merge_comment'),
        ]);

        $this->replyTicketCommand->execute($data);
    }

    /**
     * Create note for ticket
     *
     * @param TicketInterface $ticket
     * @return void
     */
    private function createNote(TicketInterface $ticket): void
    {
        $this->saveNote($ticket, $ticket->getData('merge_comment'));
    }

    /**
     * Close ticket
     *
     * @param TicketInterface $ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function closeTicket(TicketInterface $ticket): void
    {
        $ticket->setStatusId(TicketStatus::CLOSED);
        $this->ticketManagement->updateTicket($ticket);
    }

    /**
     * Merge and save notes
     *
     * @param TicketInterface $mainTicket
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function mergeNotes(TicketInterface $mainTicket): void
    {
        foreach ($this->notesToMerge as $noteToMerge) {
            $ticketToMerge = $this->ticketRepository->getById($noteToMerge->getTicketId());
            $note = sprintf(
                '<b>%s</b>:</br>%s',
                $this->getMergeNoteHeader($ticketToMerge->getUid()),
                $noteToMerge->getContent()
            );
            $this->saveNote($mainTicket, $note);
        }
    }

    /**
     * Merge cc recipients
     *
     * @param TicketInterface $mainTicket
     * @return TicketInterface
     */
    private function mergeCcRecipients(TicketInterface $mainTicket): TicketInterface
    {
        $ticketCcRecipients = (string)$mainTicket->getCcRecipients();
        $ticketCcRecipients = explode(',', $ticketCcRecipients);
        $ticketCcRecipients = array_merge($ticketCcRecipients, $this->ccRecipientsToMerge);

        foreach ($ticketCcRecipients as $key => $ticketCcRecipient) {
            $ticketCcRecipient = trim($ticketCcRecipient);
            if ($ticketCcRecipient === '') {
                unset($ticketCcRecipients[$key]);
            } else {
                $ticketCcRecipients[$key] = $ticketCcRecipient;
            }
        }

        $mainTicket->setCcRecipients(implode(',', $ticketCcRecipients));
        return $mainTicket;
    }

    /**
     * Merge tags
     *
     * @param TicketInterface $mainTicket
     * @return TicketInterface
     */
    private function mergeTags(TicketInterface $mainTicket): TicketInterface
    {
        $ticketTags = $mainTicket->getTagNames();
        $ticketTags = array_merge($ticketTags, $this->tagsToMerge);
        $mainTicket->setTagNames($ticketTags);
        return $mainTicket;
    }

    /**
     * Collect merge data
     *
     * @param TicketInterface $mainTicket
     * @param TicketInterface[] $ticketsToMerge
     * @return void
     */
    private function collectMergeData(TicketInterface $mainTicket, array $ticketsToMerge): void
    {
        $tagsToMerge = [];
        $ccRecipientsToMerge = [];
        $ticketIds = [];
        $mainTicketClientEmail = $mainTicket->getCustomerEmail();
        $clientEmails = [];
        foreach ($ticketsToMerge as $ticketToMerge) {
            if ($ticketToMerge->getCustomerEmail() !== $mainTicketClientEmail) {
                $clientEmails[] = $ticketToMerge->getCustomerEmail();
            }
            $ccRecipientsToMerge[] = explode(',', (string)$ticketToMerge->getCcRecipients());
            $tagsToMerge[] = $ticketToMerge->getTagNames();
            $ticketIds[] = $ticketToMerge->getEntityId();
        }
        $this->ccRecipientsToMerge = array_merge(...$ccRecipientsToMerge);
        $this->ccRecipientsToMerge = array_merge($this->ccRecipientsToMerge, $clientEmails);
        $this->tagsToMerge = array_merge(...$tagsToMerge);

        $this->notesToMerge = $this->messageCollectionFactory->create()
            ->addFieldToFilter(
                MessageInterface::TICKET_ID, ['in' => $ticketIds]
            )
            ->addMessageTypeFilter(MessageType::INTERNAL)
            ->sortByCreatedAt()
            ->getItems();
    }

    /**
     * Save note
     *
     * @param TicketInterface $ticket
     * @param string $internalNote
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    private function saveNote(TicketInterface $ticket, string $internalNote): void
    {
        $message = $this->messageFactory->createInternalNote();
        $message
            ->setTicketId($ticket->getEntityId())
            ->setContent($internalNote);
        $this->messageRepository->save($message);
        $message = $this->messageFactory->createForDetector($ticket);
        $message
            ->setTicketId($ticket->getEntityId())
            ->setContent(sprintf('<b>%s</b>:</br>%s', __('Note is added'), $internalNote));
        $this->messageRepository->save($message);
    }

    /**
     * Get merge note header
     *
     * @param string $ticketUid
     * @return \Magento\Framework\Phrase
     */
    private function getMergeNoteHeader(string $ticketUid): \Magento\Framework\Phrase
    {
        return __('A note from the merged ticket #%1', $ticketUid);
    }
}
