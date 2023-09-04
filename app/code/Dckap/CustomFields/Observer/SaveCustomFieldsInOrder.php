<?php
/**
* *
*  @author DCKAP Team
*  @copyright Copyright (c) 2018 DCKAP (https://www.dckap.com)
*  @package Dckap_CustomFields
*/

namespace Dckap\CustomFields\Observer;

/**
* Class SaveCustomFieldsInOrder
* @package Dckap\CustomFields\Observer
*/
class SaveCustomFieldsInOrder implements \Magento\Framework\Event\ObserverInterface
{
   /**
    * @param \Magento\Framework\Event\Observer $observer
    * @return $this
    */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $isCheckoutLoggerEnabled = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('sales/logger/enable');
     $order = $observer->getEvent()->getOrder();
     $quote = $observer->getEvent()->getQuote();

       $order->setData("shipping_options_method",$quote->getShippingOptionsMethod());
       $order->setData("shipping_options_service",$quote->getShippingOptionsService());
       $order->setData("shipping_options_account_number",$quote->getShippingOptionsAccountNumber());
       $order->setData("shipping_options_account_zip_codes",$quote->getShippingOptionsAccountZipCodes());

     //Log the request
        if($isCheckoutLoggerEnabled && $quote->getShippingOptionsMethod()) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_record_data.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('app/code/Dckap/CustomFields/Observer/SaveCustomFieldsInOrder: QuoteId- '.$quote->getId());
            $logger->info('Shipping option method - '.$quote->getShippingOptionsMethod());
            $logger->info('Shipping option service - '.$quote->getShippingOptionsService());
            $logger->info('Shipping option account number - '.$quote->getShippingOptionsAccountNumber());
            $logger->info('Shipping option account zip - '.$quote->getShippingOptionsAccountZipCodes());
            $logger->info('app/code/Dckap/CustomFields/Observer/SaveCustomFieldsInOrder: OrderId- '.$order->getEntityId());                
        }
     return $this;
  }
}
