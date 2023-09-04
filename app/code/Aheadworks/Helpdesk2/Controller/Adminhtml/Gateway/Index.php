<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page as ResultPage;

/**
 * Class Index
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class Index extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

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
        $resultPage->setActiveMenu('Aheadworks_Helpdesk2::gateways');
        $resultPage->getConfig()->getTitle()->prepend(__('Gateways'));

        return $resultPage;
    }
}
