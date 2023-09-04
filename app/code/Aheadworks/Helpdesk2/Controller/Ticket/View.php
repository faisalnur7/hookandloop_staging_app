<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Phrase;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class View
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class View extends CustomerAbstractAction
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $ticket = $this->getTicket();
        if (!$ticket) {
            $this->messageManager->addErrorMessage(__('This ticket doesn\'t exist'));
            /** ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('aw_helpdesk2/ticket');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($this->createPageTitle($ticket));

        return $resultPage;
    }

    /**
     * Get current ticket
     *
     * @return TicketInterface|null
     */
    private function getTicket()
    {
        try {
            $ticket = $this->getTicketById();
            if (!$this->isTicketBelongToCurrentCustomer($ticket)) {
                $ticket = null;
            }
        } catch (NoSuchEntityException $exception) {
            $ticket = null;
        }

        return $ticket;
    }

    /**
     * Create page title
     *
     * @param TicketInterface $ticket
     * @return Phrase
     */
    private function createPageTitle($ticket)
    {
        return __(
            '[%1] %2',
            [
                $ticket->getUid(),
                $ticket->getSubject()
            ]
        );
    }
}
