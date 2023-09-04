<?php

namespace Exinent\PartialShipping\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteRepository;
use \Magento\Checkout\Model\Session;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $quoteRepository;
    protected $_checkoutSession;

    public function __construct(
    Context $context, PageFactory $resultPageFactory, JsonFactory $resultJsonFactory, QuoteRepository $quoteRepository, Session $_checkoutSession) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $_checkoutSession;
        return parent::__construct($context);
    }

    public function execute() {

        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exinent_partialshipping.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info('controller');

        $partialShipment = $this->getRequest()->getParam('choice');
//        $logger->info($partialShipment);
        
        $quote = $this->_checkoutSession->getQuote();
//        $logger->info($quote->getId());
//        $quote = $this->quoteRepository->getActive($cartId);
        if($partialShipment){
            $quote->setPartialShipment($partialShipment);
//            $logger->info('set');
            $setdata = $quote->getPartialShipment();
//            $logger->info($setdata);
            $quote->save();
        }
        
        $result->setData(['result' => $partialShipment]);
        return $result;
    }
}