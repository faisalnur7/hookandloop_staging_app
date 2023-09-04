<?php

namespace Sivajik34\CustomFee\Plugin;

use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;

class OrderItemRepositoryPlugin
{

    /**
     * @var OrderItemExtensionFactory
     */ 
    protected $orderItemExtensionFactory;

    /**
     * @var ProductFactory
     */ 
    protected $productFactory;

     /**
     * @var OrderItemInterface
     */ 
    protected $OrderItemInterface;

      /**
     * Order custom option value
     */
      const CUSTOM_OPTION_VALUE = 'cut_to_length_charges';

    /**
     * @param OrderItemExtensionFactory $orderItemExtensionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        OrderItemExtensionFactory $orderItemExtensionFactory,
        ProductFactory $productFactory,
        OrderItemInterface $orderItemInterface
    ) {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->productFactory = $productFactory;
        $this->orderItemInterface = $orderItemInterface;
    }

    /**
     * Add "my_custom_product_attribute" to order item
     *
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    protected function addCustomOptionPrice(OrderItemInterface $orderItem)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_record_data.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $product = $this->productFactory->create();
        if($orderItem->getProductType() == 'configurable'){
            $product->load($orderItem->getProductId());
        } else {
            $product->load($product->getIdBySku($orderItem->getSku()));
        }
        //$options = $orderItem->getProduct()->getTypeInstance(true)->getOrderOptions($orderItem->getProduct());
        $options = $orderItem->getProductOptions(); 
        $optionValue = '';
        if (array_key_exists('options', $options)) {
            $customOptions = $options['options'];
            if (!empty($customOptions)) {
                $count = 0;
                foreach ($customOptions as $option) {
                    $optionId = $option['option_id'];
                    $option_price = 0;
                    $optionValues = $product->getOptionById($optionId);
                    if ($optionValues && $option['option_type']=='radio' && $count < 1) {
                        foreach($optionValues->getValues() as $values) {
                            if($values->getTitle() == 'Yes'){
                                $original_qty = $options['info_buyRequest']['qty'];
                                if(isset($options['info_buyRequest']['original_qty'])){
                                    $original_qty = $options['info_buyRequest']['original_qty'];
                                }
                                $option_price = (int)($original_qty*$values->getPrice());
                                if($option_price > 0){
                                    $logger->info('inside addCustomOptionPrice');
                                    $logger->info('order id - '.$orderItem->getOrderId());
                                    $logger->info('order item id - '.$orderItem->getId()); 
                                    $logger->info('order item custom option price - ');
                                    $logger->info('option value - '.$values->getPrice());
                                    $logger->info('option price - '.$option_price);
                                    $logger->info('qty ordered - '.$original_qty);
                                    $logger->info('product sku - '.$product->getSku());
                                    $orderItemExtension = $this->orderItemExtensionFactory->create();
                                    $orderItemExtension->setCustomOptionPrice($option_price);
                                    $orderItem->setCutToLengthCharges($option_price);
                                    $orderItem->setExtensionAttributes($orderItemExtension);
                                }
                            } 
                        }
                    }
                    $count ++;
                }
            }
        }
        return $orderItem;
    }

     /* Add "my_custom_product_attribute" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $orderItem)
    {
        $customAttribute = $orderItem->getData('cut_to_length_charges');

        $extensionAttributes = $orderItem->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();

        $extensionAttributes->setCustomOptionPrice($customAttribute);
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }

    /**
     * Add "my_custom_product_attribute" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemSearchResultInterface $searchResult
     *
     * @return OrderItemSearchResultInterface
     */
    public function afterGetList(OrderItemRepositoryInterface $subject, OrderItemSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            $order = $this->addCustomOptionPrice($order);
        }
        return $searchResult;
    }
}
