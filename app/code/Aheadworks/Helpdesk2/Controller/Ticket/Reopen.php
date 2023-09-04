<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Reopen
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class Reopen extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $reopenCommand;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $reopenCommand
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $reopenCommand,
        Session $customerSession
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->reopenCommand = $reopenCommand;
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
            $this->reopenCommand->execute(['ticket' => $ticket]);
            $this->messageManager->addSuccessMessage(__('Ticket was reopened'));
            if ($this->customerSession->authenticate()) {
                $resultRedirect->setPath('*/*/view', ['id' => $ticket->getEntityId()]);
            } else {
                $resultRedirect->setPath('*/*/external', ['key' => $ticket->getExternalLink()]);
            }
            return $resultRedirect;
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while reopening the ticket'));
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
