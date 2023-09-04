<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Download
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment
 */
class Download extends Action
{
    /**
     * @var CommandInterface
     */
    private $downloadCommand;

    /**
     * @param Context $context
     * @param CommandInterface $downloadCommand
     */
    public function __construct(
        Context $context,
        CommandInterface $downloadCommand
    ) {
        parent::__construct($context);
        $this->downloadCommand = $downloadCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $attachmentId = $this->getRequest()->getParam('attachment_id');
            if ($attachmentId) {
                return $this->downloadCommand->execute(['attachment_id' => $attachmentId]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while downloading attachment'));
        }

        /** ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addErrorMessage(__('File not found'));
        return $resultRedirect->setPath('*/*');
    }
}
