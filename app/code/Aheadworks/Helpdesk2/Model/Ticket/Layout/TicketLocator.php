<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class TicketLocator
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout
 */
class TicketLocator
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Get ticket from request
     *
     * @param RequestInterface $request
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    public function getTicket($request)
    {
        $ticketId = $request->getParam('id');
        if ($ticketId) {
            return $this->ticketRepository->getById($ticketId);
        }
        $link = $request->getParam('key');
        if ($link) {
            return $this->ticketRepository->getByExternalLink($link);
        }

        throw new NoSuchEntityException(__('This ticket doesn\'t exist'));
    }
}
