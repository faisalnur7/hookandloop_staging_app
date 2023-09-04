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

class AfterGetPrice {

    protected $scopeConfig;
    protected $request;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Request\Http $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * Multiplying measurment sold in size with product price on product page
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $product, $result) {
        $id = $product->getId();
        $sku = $product->getSku();
        $brand_id = substr($sku, 0, 2);
        $actionName = $this->request->getFullActionName();
       
        $moduleArr = ['Magento_Catalog', 'Magento_Customer'];
        if (in_array($this->request->getControllerModule(), $moduleArr)) {
            $result = $this->calculate($result, $product, $id, $brand_id);
        } 
        return $result;
    }

    public function calculate($price, $product, $id, $brand_id) {
        $actionName = $this->request->getFullActionName();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $qty = $product->getQty();
        if ($product->getData("special_price")) {
            $finalPrice = $product->getData("special_price");
        } else {
            $finalPrice = $product->getData("price");
        }
        if ($actionName == 'catalog_product_view'||$actionName == 'cms_page_view') {
            $productQuantity = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($id);
            if ($product->getMeasurementSoldInSize() > $productQuantity->getMinSaleQty()) {
                $finalPrice *= $product->getMeasurementSoldInSize();
                $product->setFinalPrice($finalPrice);
                return $finalPrice;
            }
        }
        return $finalPrice;  
    }

}
