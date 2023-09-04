<?php
namespace Aheadworks\Helpdesk2\Controller;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class TicketAbstract
 *
 * @package Aheadworks\Helpdesk2\Controller
 */
abstract class TicketAbstract extends Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var TicketRepositoryInterface
     */
    protected $ticketRepository;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->ticketRepository = $ticketRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Get ticket by ID
     *
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    protected function getTicketById()
    {
        $ticketId = $this->getRequest()->getParam('id');
        return $this->ticketRepository->getById($ticketId);
    }

    /**
     * Get ticket by external link
     *
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    protected function getTicketByExternalLink()
    {
        $ticketLink = $this->getRequest()->getParam('key');
        return $this->ticketRepository->getByExternalLink($ticketLink);
    }
}
