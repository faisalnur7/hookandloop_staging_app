<?php
namespace Ravedigital\Ordercreate\Controller\Adminhtml\Missingorder;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' Create Missing Orders '));

         return $resultPage;
    }

    public function templ()
    {
        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('Ravedigital_Ordercreate::createorder.phtml')
                ->toHtml();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ravedigital_Ordercreate::sales');
    }
}
