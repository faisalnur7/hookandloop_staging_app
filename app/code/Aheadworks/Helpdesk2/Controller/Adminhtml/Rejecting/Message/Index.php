<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Message
 */
class Index extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::rejected_messages';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return ResultPage
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Helpdesk2::rejected_messages');
        $resultPage->getConfig()->getTitle()->prepend(__('Rejected Messages'));

        return $resultPage;
    }
}
