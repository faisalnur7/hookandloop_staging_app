<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;

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
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::rejecting_patterns';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RejectingPatternRepositoryInterface $patternRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RejectingPatternRepositoryInterface $patternRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->patternRepository = $patternRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $patternId = (int)$this->getRequest()->getParam(RejectingPatternInterface::ID);
        if ($patternId) {
            try {
                $this->patternRepository->deleteById($patternId);
                $this->messageManager->addSuccessMessage(__('Pattern was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the pattern'));

        return $resultRedirect->setPath('*/*/');
    }
}
