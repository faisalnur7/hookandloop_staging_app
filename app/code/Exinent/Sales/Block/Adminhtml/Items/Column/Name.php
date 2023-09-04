<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Exinent\Sales\Block\Adminhtml\Items\Column;

/**
 * Sales Order items name column renderer
 *
 * @api
 * @since 100.0.2
 */
class Name extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems {

    protected $_optionFactory;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration, \Magento\Framework\Registry $registry, \Magento\Catalog\Model\Product\OptionFactory $optionFactory, array $data = []
    ) {
        $this->_optionFactory = $optionFactory;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value) {
        $remainder = '';
        $value = $this->truncateString($value, 55, '', $remainder);
        $result = ['value' => nl2br($value), 'remainder' => nl2br($remainder)];

        return $result;
    }

    public function getItem() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $item = $this->_getData('item');
        if ($item->getProductType() == 'configurable') {
            $allitems = $item->getOrder()->getAllItems();
            $id = $item->getId() + 1;
            foreach ($allitems as $allitem) {
                if ($allitem->getId() == $id) {
                    $name = $allitem['name'];
                }
            }
            if (true) {
                $product = $productRepository->get($item['sku']);
                if ($product && isset($name)) {
                    $item['name'] = $name;
                }
                return $item;
            }
        } else if (true) {

            return $item;
        } else {
            return $item->getOrderItem();
        }
    }

    public function getSku() {
        $item = $this->getItem();
        if ($item->getProductType() == 'configurable') {
            $allitems = $item->getOrder()->getAllItems();
            $id = $item->getId() + 1;
            foreach ($allitems as $allitem) {
                if ($allitem->getId() == $id) {
                    return $allitem['sku'];
                }
            }
        }
        return $this->getItem()->getSku();
    }

}
