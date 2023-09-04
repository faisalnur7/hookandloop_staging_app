<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Reply
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Reply implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var TicketInterfaceFactory
     */
    private $ticketFactory;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketRepositoryInterface $ticketRepository
     * @param TicketManagementInterface $ticketManagement
     * @param TicketInterfaceFactory $ticketFactory
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TicketRepositoryInterface $ticketRepository,
        TicketManagementInterface $ticketManagement,
        TicketInterfaceFactory $ticketFactory,
        MessageInterfaceFactory $messageFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketRepository = $ticketRepository;
        $this->ticketManagement = $ticketManagement;
        $this->ticketFactory = $ticketFactory;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($ticketData)
    {
        $ticket = $this->getTicketObject($ticketData);
        $message = $this->prepareMessage($ticketData, $ticket);
        $this->ticketManagement->updateTicket($ticket);
        $message->setTicketId($ticket->getEntityId());
        $this->ticketManagement->createNewMessage($message);

        return $ticket;
    }

    /**
     * Get ticket object
     *
     * @param array $ticketData
     * @return TicketInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getTicketObject($ticketData)
    {
        $ticket = $this->ticketRepository->getById($ticketData[TicketInterface::ENTITY_ID] ?? null);
        $this->dataObjectHelper->populateWithArray(
            $ticket,
            $ticketData,
            TicketInterface::class
        );

        return $ticket;
    }

    /**
     * Prepare message
     *
     * @param array $data
     * @param TicketInterface $ticket
     * @return MessageInterface
     */
    private function prepareMessage($data, $ticket)
    {
        /** @var MessageInterface $message */
        $message = $this->messageFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $message,
            $data,
            MessageInterface::class
        );
        $message->setTicketId($ticket->getEntityId());
        $message->setStatusId($ticket->getStatusId());
        $message->setAgentId($ticket->getAgentId());
        return $message;
    }
}
