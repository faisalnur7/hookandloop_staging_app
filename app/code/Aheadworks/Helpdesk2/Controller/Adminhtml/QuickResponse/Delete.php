<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::quick_responses';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CommandInterface
     */
    private $deleteCommand;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CommandInterface $deleteCommand
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CommandInterface $deleteCommand
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->deleteCommand = $deleteCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quickResponseId = (int)$this->getRequest()->getParam(QuickResponseInterface::ID);
        if ($quickResponseId) {
            try {
                $this->deleteCommand->execute([QuickResponseInterface::ID => $quickResponseId]);
                $this->messageManager->addSuccessMessage(__('Quick response was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting quick response'));

        return $resultRedirect->setPath('*/*/');
    }
}
