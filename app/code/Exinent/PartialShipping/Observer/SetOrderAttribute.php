<?php

namespace Exinent\PartialShipping\Observer;

use Magento\Sales\Model\Order\Item;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SetOrderAttribute implements ObserverInterface {

    public function execute(EventObserver $observer) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exinent_partialshipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $quote = $observer->getQuote();
        $partialShipment = $quote->getPartialShipment();
        if (!empty($partialShipment)) {
            $order = $observer->getOrder();
            $order->setPartialShipment($partialShipment);
        }
        $order = $observer->getOrder();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $orderItem->setStatus(Item::STATUS_PARTIAL);
            $logger->info($orderItem->getState().'_'.$orderItem->getStatus());
        }
        return $this;
    }

}
