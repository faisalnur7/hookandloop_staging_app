<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Update
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Update implements CommandInterface
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
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketRepositoryInterface $ticketRepository
     * @param TicketManagementInterface $ticketManagement
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TicketRepositoryInterface $ticketRepository,
        TicketManagementInterface $ticketManagement
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketRepository = $ticketRepository;
        $this->ticketManagement = $ticketManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($ticketData)
    {
        $ticket = $this->getTicketObject($ticketData);
        $this->dataObjectHelper->populateWithArray(
            $ticket,
            $ticketData,
            TicketInterface::class
        );

        return $this->ticketManagement->updateTicket($ticket);
    }

    /**
     * Get gateway object
     *
     * @param array $ticketData
     * @return TicketInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getTicketObject($ticketData)
    {
        return $this->ticketRepository->getById($ticketData[TicketInterface::ENTITY_ID] ?? null);
    }
}
