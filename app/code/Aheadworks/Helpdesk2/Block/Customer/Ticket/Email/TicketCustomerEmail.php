<?php
declare(strict_types = 1);

namespace Aheadworks\Helpdesk2\Block\Customer\Ticket\Email;

use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class TicketCustomerEmail extends Template
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        array $data = []
    ) {
        $this->ticketRepository = $ticketRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get ticket
     *
     * @return Ticket|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTicket(): ?Ticket
    {
        $ticket = $this->getData('ticket');
        if ($ticket !== null) {
            return $ticket;
        }

        $ticketId = (int)$this->getData('ticket_id');
        if ($ticketId) {
            $ticket = $this->ticketRepository->getById($ticketId);
            $this->setData('ticket', $ticket);
        }

        return $this->getData('ticket');
    }
}
