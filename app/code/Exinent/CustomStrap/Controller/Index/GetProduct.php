<?php

namespace Exinent\CustomStrap\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

class GetProduct extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
//    public function __construct(Context $context, array $data = []) {
//        parent::__construct($context, $data);
//    }

    public function execute() {
        $brand = $this->getRequest()->getPost('brand');
        $width = $this->getRequest()->getPost('width');
        $strapType = $this->getRequest()->getPost('strapType');
        $strapLength = $this->getRequest()->getPost('strapLength');
        $strapLength = ceil($strapLength);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $productCollections = $objectManager->create('\Exinent\CustomStrap\Model\Customstrap')->getCollection()
                ->addFieldToFilter('brand', array('eq' => $brand))
                ->addFieldToFilter('width', array('eq' => $width))
                ->addFieldToFilter('strap_type', array('eq' => $strapType))
                ->addFieldToFilter('length', array('eq' => $strapLength))
                ->load();
        if (count($productCollections) > 0) {
            foreach ($productCollections as $productCollection) {
                $sku = $productCollection->getSku();
                $productRepository = $objectManager->get('Magento\Catalog\Model\Product');
                $_product = $productRepository->loadByAttribute('sku', $sku);
                if (!empty($_product)) {
                    $product_id = $_product->getId();
                    $price = number_format($_product->getPrice(), 2, '.', '');
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $productStockObj = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($product_id);
                    $minQty = $productStockObj->getMinQty();
                    $data = array('sku' => $sku, 'price' => $price, 'minQty' => $minQty);
                    $result->setData($data);
                    return $result;
                } else {
                    $result->setData("Not found");
                    return $result;
                }
            }
        } else {
            $result->setData("Not found");
            return $result;
        }
    }

}
