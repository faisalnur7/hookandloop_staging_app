<?php

/**
 * Exinent_Catalog Module
 *
 * @category    checkout
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class CustomPrice implements ObserverInterface {

    protected $scopeConfig;
    protected $request;
    protected $_productRepository;
    protected $cart;
    protected $customerSession;
    protected $_product;
    protected $checkoutSession;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Checkout\Model\Cart $cart, 
    \Magento\Customer\Model\Session $customerSession, 
    \Magento\Catalog\Model\ProductRepository $productRepository, 
    \Magento\Framework\App\Request\Http $request,
    \Magento\Catalog\Model\Product $product,
    \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        $this->request = $request;
        $this->_product = $product;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {           
        $item = $observer->getEvent()->getData('quote_item');
        $qty = $item->getQty();
        $qtyadded = $item->getQtyToAdd();
        $updateQuantity = $this->cart->getQuote()->getOriginalItemsQty();         
        $actionName = $this->request->getFullActionName();
        $simplepro = $this->_productRepository->get($item->getProduct()->getSku());
    
        if ($actionName != 'sales_order_reorder') {            
            $this->setOriginalQty();
            if ($qty == $qtyadded) {
                 if($simplepro->getMeasurementSoldInSize()>1){                      
                    $item->setQty($qty * $simplepro->getMeasurementSoldInSize());
                    $item->save();
                 }
            } else {
                if($simplepro->getMeasurementSoldInSize()>1){
                    $qty = $qty - $qtyadded;
                    $qtywithsoldinsize = $qtyadded * $simplepro->getMeasurementSoldInSize();
                    $qty = $qty + $qtywithsoldinsize;                   
                    $item->setQty($qty);
                    $item->save();
                }
            }
        }   
    }

    public function setOriginalQty()
    {
        $itemsVisible = $this->cart->getQuote()->getAllVisibleItems();
        $totalqty = 0;
        $getCartQty = $this->cart->getQuote()->getOriginalItemsQty();

        if($this->_checkoutSession->getHasBothProducts()){
            $this->_checkoutSession->unsHasBothProducts();  
            return;
        }

        foreach ($itemsVisible as $item) { 

            if ($item->getProductType() == 'configurable') {
                $options = $item->getOptionByCode('simple_product')->getData();
                $productId = $options["product_id"];
                $child = $this->_product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
               
                $qty = $item->getQty();
                $qtyadded = $item->getQtyToAdd();

                if($qty > $measurement && $getCartQty == 0){
                   $computed_qty = $qty / $measurement;
                   $totalqty += $computed_qty;
                }else{
                    $getCartQty += $qtyadded;
                    $totalqty = $getCartQty;
                }                
                
            } else {
                $productId = $item->getProductId();
                $child = $this->_product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $qtyadded = $item->getQtyToAdd();
               
               if($qty > $measurement && $getCartQty == 0){
                   $computed_qty = $qty / $measurement;
                   $totalqty += $computed_qty;
                }else{
                    $getCartQty += $qtyadded;
                    $totalqty = $getCartQty;
                }  
            }
        } 
        $this->cart->getQuote()->setOriginalItemsQty($totalqty)->save();  
    }
}
