<?php

namespace Dckap\CustomFields\Helper;
use Magento\Checkout\Model\Session;

/**
 * Class Config
 * @package MW\Onestepcheckout\Helper
 */

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Session $session
    ) {
        parent::__construct($context);
        $this->_checkoutSession= $session;
    }

     /*Shipping option*/
    public function getShippingOption()
    {  
        $fedexData = $this->scopeConfig->getValue('carriers/shippingoptions/fedex_service_option');
        $upsData = $this->scopeConfig->getValue('carriers/shippingoptions/ups_service_option');
        $dhlData = $this->scopeConfig->getValue('carriers/shippingoptions/dhl_service_option');      

       $allShippingOption = array('fedex'=>$fedexData, 'ups'=>$upsData, 'dhl'=>$dhlData );
       return json_encode($allShippingOption);
    }

    public function getMethodOptions() {
        $items = array();
        $isFedex = $this->scopeConfig->getValue('carriers/shippingoptions/fedex_active'); 
        $isUps = $this->scopeConfig->getValue('carriers/shippingoptions/ups_active'); 
        $isDhl = $this->scopeConfig->getValue('carriers/shippingoptions/dhl_active'); 

        $key =1;
        if($isFedex == 1)
        {
           $fedexMethod = 'Fedex';
           $items[$key]["value"] = strtolower($fedexMethod);
           $items[$key]["label"] = $fedexMethod;
           $key++;
        }
        if($isUps == 1)
        {
           $upsMethod = 'UPS';
           $items[$key]["value"] = strtolower($upsMethod);
           $items[$key]["label"] = $upsMethod;
           $key++;
        }
        if($isDhl == 1)
        {
           $dhlMethod = 'DHL';
           $items[$key]["value"] = strtolower($dhlMethod);
           $items[$key]["label"] = $dhlMethod;
           $key++;
        }
        return $items;
    }

    public function getServiceOptions() {
        $items = array();

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/service_option');
        $methods = explode(',', $shippingOptionData);                                
       foreach ($methods as $key => $method) {
           $method = str_replace('"', '', $method);
           $items[$key+1]["value"] = strtolower($method);
           $items[$key+1]["label"] = $method;
        }        
        return $items;
    }

    public function getDhlServiceOptions() {
        $items = array();

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/dhl_service_option');
        $methods = explode(',', $shippingOptionData);                                
       foreach ($methods as $key => $method) {
           $method = str_replace('"', '', $method);
           $items[$key+1]["value"] = strtolower($method);
           $items[$key+1]["label"] = $method;
        }        
        return $items;
    }

    public function getFedexServiceOptions() {
        $items = array();

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/fedex_service_option');
        $methods = explode(',', $shippingOptionData);                                
       foreach ($methods as $key => $method) {
           $method = str_replace('"', '', $method);
           $items[$key+1]["value"] = strtolower($method);
           $items[$key+1]["label"] = $method;
        }        
        return $items;
    }

    public function getUpsServiceOptions() {
        $items = array();

        $shippingOptionData = $this->scopeConfig->getValue('carriers/shippingoptions/ups_service_option');
        $methods = explode(',', $shippingOptionData);                                
       foreach ($methods as $key => $method) {
           $method = str_replace('"', '', $method);
           $items[$key+1]["value"] = strtolower($method);
           $items[$key+1]["label"] = $method;
        }        
        return $items;
    }

    public function getSelectedMethodOption() {
        return $this->getUpsServiceOptions(); 
     }
      public function getEtaCheckoutUsps()
    {
       return $this->_checkoutSession->getEtaCheckoutUsps();
        
    }

     public function getEtaCheckoutUps()
    {
        return $this->_checkoutSession->getEtaCheckoutUps();
    }

     public function getEtaCheckoutFedex()
    {
        return $this->_checkoutSession->getEtaCheckoutFedex();
    }
}
