<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Ravedigital\CoreUpdate\Model\Wishlist;

class Wishlist extends \Magento\Wishlist\Model\Wishlist
{
    
    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false)
    {
        /*
         * Always load product, to ensure:
         * a) we have new instance and do not interfere with other products in wishlist
         * b) product has full set of attributes
         */
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
            // Maybe force some store by wishlist internal properties
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();
        } else {
            $productId = (int)$product;
            if (isset($buyRequest) && $buyRequest->getStoreId()) {
                $storeId = $buyRequest->getStoreId();
            } else {
                $storeId = $this->_storeManager->getStore()->getId();
            }
        }

        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Cannot specify product.'));
        }

        if ($buyRequest instanceof \Magento\Framework\DataObject) {
            $_buyRequest = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $isInvalidItemConfiguration = false;
            try {
                $buyRequestData = $this->serializer->unserialize($buyRequest);
                if (!is_array($buyRequestData)) {
                    $isInvalidItemConfiguration = true;
                }
            } catch (\InvalidArgumentException $exception) {
                $isInvalidItemConfiguration = true;
            }
            if ($isInvalidItemConfiguration) {
                throw new \InvalidArgumentException('Invalid wishlist item configuration.');
            }
            $_buyRequest = new \Magento\Framework\DataObject($buyRequestData);
        } elseif (is_array($buyRequest)) {
            $_buyRequest = new \Magento\Framework\DataObject($buyRequest);
        } else {
            $_buyRequest = new \Magento\Framework\DataObject();
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $cartCandidates = $product->getTypeInstance()->processConfiguration($_buyRequest, clone $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $errors = [];
        $items = [];

        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setWishlistStoreId($storeId);

            //$qty = $candidate->getQty() ? $candidate->getQty() : 1;

            if ($product->getMeasurementSoldInSize()!='' && $product->getMeasurementSoldInSize() > 0) {
                $qty_value = $candidate->getQty() ? $candidate->getQty() : 1;
                $qty = $qty_value / $product->getMeasurementSoldInSize();
            } else {
                $qty = $candidate->getQty() ? $candidate->getQty() : 1;
            }

            // No null values as qty. Convert zero to 1.
            $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
            $items[] = $item;

            // Collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }

        $this->_eventManager->dispatch('wishlist_product_add_after', ['items' => $items]);

        return $item;
    }
}
