<?php

/**
 * Exinent_Checkout Module 
 *
 * @category    checkout
 * @package     Exinent_Checkout
 * @author      pawan
 *
 */

namespace Exinent\Checkout\Block\Cart;

class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell {

    public function getItems() {
        $items = $this->getData('items');
        if ($items === null) {
            $items = [];
            $ninProductIds = $this->_getCartProductIds();
            if ($ninProductIds) {
                $lastAdded = (int) $this->_getLastAddedProductId();
                if ($lastAdded) {
                    $collection = $this->_getCollection()->addProductFilter($lastAdded);
                    if (!empty($ninProductIds)) {
                        $collection->addExcludeProductFilter($ninProductIds);
                    }
                    $collection->setPositionOrder()->load();

                    foreach ($collection as $item) {
                        //$ninProductIds[] = $item->getId();
                        $items[] = $item;
                    }
                }

                if (count($items) < $this->_maxItemCount) {
                    if (is_array($this->_getCartProductIds()) && is_array($this->_itemRelationsList->getRelatedProductIds($this->getQuote()->getAllItems()))) {
                        $filterProductIds = array_merge(
                                $this->_getCartProductIds(), $this->_itemRelationsList->getRelatedProductIds($this->getQuote()->getAllItems())
                        );
                        $collection = $this->_getCollection()->addProductFilter(
                                        $filterProductIds
                                )->addExcludeProductFilter(
                                        $ninProductIds
                                )->setPageSize(
                                        $this->_maxItemCount - count($items)
                                )->setGroupBy()->setPositionOrder()->load();
                        foreach ($collection as $item) {
                            $items[] = $item;
                        }
                    }
                }
            }

            $this->setData('items', $items);
        }
        return $items;
    }

}
