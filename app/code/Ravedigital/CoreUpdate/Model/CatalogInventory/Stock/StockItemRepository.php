<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\CatalogInventory\Stock;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockItemRepository extends \Magento\CatalogInventory\Model\Stock\StockItemRepository
{

    /**
     * @inheritdoc
     */
    public function get($stockItemId)
    {
        $stockItem = $this->stockItemFactory->create();
        $this->resource->load($stockItem, $stockItemId, 'product_id');
        if (!$stockItem->getItemId()) {
            throw new NoSuchEntityException(
                __('The stock item with the "%1" ID wasn\'t found. Verify the ID and try again.', $stockItemId)
            );
        }
        return $stockItem;
    }
}
