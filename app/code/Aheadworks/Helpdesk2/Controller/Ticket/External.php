<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Phrase;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;

/**
 * Class External
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class External extends TicketAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $ticket = $this->getTicketByExternalLink();
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This ticket doesn\'t exist'));
            /** ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($this->createPageTitle($ticket));

        return $resultPage;
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
