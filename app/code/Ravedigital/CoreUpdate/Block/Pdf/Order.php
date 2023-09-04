<?php
namespace Ravedigital\CoreUpdate\Block\Pdf;

class Order extends \Fooman\PdfCustomiser\Block\Pdf\Order
{
    
    /**
     * get visible order items
     * overridden as property different for orders
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    // public function getVisibleItems()
    // {
    //     $items = [];
    //     $allItems = $this->getSalesObject()->getItems();
    //     if ($allItems) {
    //         foreach ($allItems as $item) {
    //             if ($this->shouldDisplayItem($item)) {
    //                 $items[] = $this->prepareItem($item);
    //             }
    //         }
    //     }
    //     if ($this->getSortColumnsBy()) {
    //         uasort($items, [$this, 'sort']);
    //     }

    //     return $items;
    // }

    public function getVisibleItems()
    {
        $items = [];
        $allItems = $this->getSalesObject()->getItems();
        if ($allItems) {
            foreach ($allItems as $item) {
                if ($this->shouldDisplayItem($item)) {
                //Changes regarding item title starts here.. We need to overirde this file and do it in our custom module.
                    if ($item->getProductType() == 'configurable') {
                        $options = $item->getProductOptions();
                        if (isset($options['attributes_info']) && isset($options['simple_sku']) && isset($options['simple_name'])) {
                            $item['name'] = $options['simple_name'];
                                    $item['sku'] = $options['simple_sku'];
                        } else {
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');

                            $allitems = $item->getOrder()->getAllItems();
                            $id = $item->getId() + 1;
                            foreach ($allitems as $allitem) {
                                if ($allitem->getId() == $id) {
                                    $name = $allitem['name'];
                                    $sku = $allitem['sku'];
                                }
                            }
                            if (true) {
                                $product = $productRepository->get($item['sku']);
                                if ($product) {
                                    $item['name'] = $name;
                                    $item['sku'] = $sku;
                                }
                            }
                        }
                    }

                     //Changes end here
                    $items[] = $this->prepareItem($item);
                }
            }
        }
        if ($this->getSortColumnsBy()) {
            uasort($items, [$this, 'sort']);
        }
        return $items;
    }
}
