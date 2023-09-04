<?php

namespace Ravedigital\Ordercreate\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /** @var \Magento\Sales\Api\Data\OrderItemExtensionFactory */
    protected $orderItemExtensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderItemExtensionFactory $orderItemExtensionFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory
    ) {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
    }

    /**
     * Add "raw_product_options" extension attribute to order item data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        foreach ($order->getAllItems() as &$item) {
            $extensionAttributes = $item->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->orderItemExtensionFactory->create();
            $productOptionsArray = $item->getProductOptions();
            if (count($productOptionsArray) && isset($productOptionsArray['options'])) {
                $extensionAttributes->setRawProductOptions(serialize($productOptionsArray));
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
        return $order;
    }
    /**
     * Add "raw_product_options" extension attribute to order item data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            foreach ($order->getAllItems() as &$item) {
                $extensionAttributes = $item->getExtensionAttributes();
                $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->orderItemExtensionFactory->create();
                /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logger.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info(json_encode(get_class_methods($extensionAttributes)));*/
                $productOptionsArray = $item->getProductOptions();
                if (count($productOptionsArray) && isset($productOptionsArray['options'])) {
                    $extensionAttributes->setRawProductOptions(serialize($productOptionsArray));
                    $item->setExtensionAttributes($extensionAttributes);
                }
            }
        }
        return $searchResult;
    }
}