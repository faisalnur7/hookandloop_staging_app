<?php

namespace Ravedigital\CheckoutSummery\Plugin;

class ConfigProviderPlugin extends \Magento\Framework\Model\AbstractModel
{

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $items = $result['totalsData']['items'];
             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        for ($i=0; $i<count($items); $i++) {

             $quoteId = $items[$i]['item_id'];
             $quoteSimple = $objectManager->create('\Magento\Quote\Model\Quote\Item')->load($quoteId);
             $simpleProductSku = $quoteSimple->getSku();
                  
             $items[$i]['childsku'] = $simpleProductSku;
             $product = $objectManager->create('\Magento\Catalog\Model\ProductRepository')->get($simpleProductSku);
             $simpleProductName = $product->getName();
             $items[$i]['name'] = $simpleProductName;
             $result['quoteItemData'][$i]['name'] = $simpleProductName;

             $result['quoteItemData'][$i]['childsku'] = $simpleProductSku;
        }
          
        $result['totalsData']['items'] = $items;
        
        return $result;
    }
}
