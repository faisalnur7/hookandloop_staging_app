<?php

/**
 * Exinent_Catalog Module 
 *
 * @category    catalog
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Model;

class Cart extends \Magento\Checkout\Model\Cart {

    /**
     * Convert order item to quote item
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param true|null $qtyFlag if is null set product qty like in order
     * @return $this
     */
    public function addOrderItem($orderItem, $qtyFlag = null) {

        /* @var $orderItem \Magento\Sales\Model\Order\Item */
        if ($orderItem->getParentItem() === null) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                /**
                 * We need to reload product in this place, because products
                 * with the same id may have different sets of order attributes.
                 */
                $product = $this->productRepository->getById($orderItem->getProductId(), false, $storeId, true);
            } catch (NoSuchEntityException $e) {
                return $this;
            }


            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new \Magento\Framework\DataObject($info);

            $qty = 1;

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productQuantity = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($product->getId());

            if ($product->getMeasurementSoldInSize() > $productQuantity->getMinSaleQty()) {
                $qty = $product->getMeasurementSoldInSize();
            } else if ($productQuantity->getMinSaleQty() > 1) {
                $qty = $productQuantity->getMinSaleQty();
            }
            if ($qtyFlag === null) {
                if ($qty > $orderItem->getQtyOrdered()) {
                    $info->setQty($qty);
                } else {
                    $info->setQty($orderItem->getQtyOrdered());
                }
            } else {
                $info->setQty($qty);
            }

            $this->addProduct($product, $info);
        }
        return $this;
    }

}
