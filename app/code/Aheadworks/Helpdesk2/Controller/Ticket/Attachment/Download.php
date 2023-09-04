<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket\Attachment;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Search\AttachmentChecker;

/**
 * Class Download
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket\Attachment
 */
class Download extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $downloadCommand;

    /**
     * @var AttachmentChecker
     */
    private $attachmentChecker;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $downloadCommand
     * @param Session $customerSession
     * @param AttachmentChecker $attachmentChecker
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $downloadCommand,
        Session $customerSession,
        AttachmentChecker $attachmentChecker
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->downloadCommand = $downloadCommand;
        $this->attachmentChecker = $attachmentChecker;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $ticket = $this->getTicketByExternalLink();
            $attachmentId = $this->getRequest()->getParam('attachment_id', 0);
            if ($this->attachmentChecker->isAttachmentBelongToTicket($attachmentId, $ticket->getEntityId())) {
                return $this->downloadCommand->execute(['attachment_id' => $attachmentId]);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while downloading attachment'));
        }

        /** ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addErrorMessage(__('File not found'));
        return $resultRedirect->setPath('*/*');
    }
}
