<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Escalate
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class Escalate extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $escalateCommand;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $escalateCommand
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $escalateCommand,
        Session $customerSession
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->escalateCommand = $escalateCommand;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $ticket = $this->getTicketByExternalLink();
            $this->escalateCommand->execute(
                [
                    'ticket' => $ticket,
                    'escalation-message' => $this->getRequest()->getParam('escalation-message')
                ]
            );
            $this->messageManager->addSuccessMessage(__('Ticket was successfully escalated'));
            if ($this->customerSession->authenticate()) {
                $resultRedirect->setPath('*/*/view', ['id' => $ticket->getEntityId()]);
            } else {
                $resultRedirect->setPath('*/*/external', ['key' => $ticket->getExternalLink()]);
            }
            return $resultRedirect;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while escalating the ticket'));
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
