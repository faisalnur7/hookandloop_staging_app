<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Rate
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
class Rate extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $rateCommand;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $rateCommand
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $rateCommand,
        Session $customerSession
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->rateCommand = $rateCommand;
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

            $this->rateCommand->execute([
                'ticket' => $ticket,
                'rating' => $this->getRequest()->getParam('rating')
            ]);
            if ($ticket->getCustomerId() === $this->customerSession->getCustomerId()
                || !$this->customerSession->isLoggedIn()) {
                $this->messageManager->addSuccessMessage(__('Ticket Rating was successfully updated.'));
            }

            if ($this->customerSession->authenticate()) {
                $resultRedirect->setPath('*/*/view', ['id' => $ticket->getEntityId()]);
            } else {
                $resultRedirect->setPath('*/*/external', ['key' => $ticket->getExternalLink()]);
            }
            return $resultRedirect;
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the ticket.'));
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
