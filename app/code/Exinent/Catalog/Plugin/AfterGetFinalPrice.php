<?php

/**
 * Exinent_Catalog Module 
 *
 * @category    checkout
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Plugin;

//use Magento\Framework\Event\ObserverInterface;


class AfterGetFinalPrice {

    protected $scopeConfig;
    protected $request;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Request\Http $request, \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->_eventManager = $eventManager;
    }

    public function aftergetFinalPrice($id, $price, $qty, $product) {
        $finalPrice = $price;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getData('entity_id'));
        if ($qty === null || $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }
        $productQuantity = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($id);
        if ($product->getMeasurementSoldInSize() > $productQuantity->getMinSaleQty()) {
            $finalPrice *= $product->getMeasurementSoldInSize();
            $product->setFinalPrice($finalPrice);
            $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);
            $finalPrice = $product->getData('final_price');
            $finalPrice = max(0, $finalPrice);
            $product->setFinalPrice($finalPrice);
        }
        return $finalPrice;  
    }

}
