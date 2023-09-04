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

class FinalPricemanipulation implements ObserverInterface {

    protected $scopeConfig;
    protected $request;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Request\Http $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/observerslogs.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        $qty = $observer->getEvent()->getQty();
        $actionName = $this->request->getFullActionName();
//        $logger->info($actionName);
//        if (empty($qty) || ($qty == 1)) {
//            $productQuantity = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($pId);
//            if ($product->getMeasurementSoldInSize() > $productQuantity->getMinSaleQty()) {
//                if ($actionName == 'catalog-product-view') {
//                    $finalPrice = $product->getData("final_price");
//                    $finalPrice *= $productQuantity->getMinSaleQty();
//                    $product->setFinalPrice($finalPrice);
//                }
//            } elseif ($productQuantity->getMinSaleQty() > 1) {
//                $actionName = $this->request->getFullActionName();
//                $logger->info($actionName);
//                if ($actionName == 'catalog-product-view') {
//                    $finalPrice = $product->getData("final_price");
//                    $finalPrice *= $productQuantity->getMinSaleQty();
//                    $product->setFinalPrice($finalPrice);
//                }
//            }
//        }
        return $this;
    }

}
