<?php

namespace Exinent\Customzip\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteRepository;
use \Magento\Checkout\Model\Session;

class Mcart extends \Magento\Framework\App\Action\Action {

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
        $result =  $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPost();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $product =$objectManager->get('\Magento\Catalog\Model\Product');
        // $totalItems = $cart->getQuote()->getItemsCount();
        // $totalQuantity = $cart->getQuote()->getItemsQty();

        // echo $totalQuantity;
        $itemsVisible=$cart->getQuote()->getAllVisibleItems();
                $totalqty = 0;
            foreach ($itemsVisible as $item) {
            if ($item->getProductType() == 'configurable') {
                $options = $item->getOptionByCode('simple_product')->getData();
                $productId = $options["product_id"];
                $child = $product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            } else {
                $productId = $item->getProductId();
                $child = $product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            }

        }
    //    echo json_encode('total_quantity',$totalqty);
        $response = ['success' => 'true','total_quantity'=>$totalqty];

        $result->setData($response);

        return $result;

    }
}


