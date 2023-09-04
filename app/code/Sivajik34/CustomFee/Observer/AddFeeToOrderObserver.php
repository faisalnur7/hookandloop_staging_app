<?php

namespace Sivajik34\CustomFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as checkoutSession;

class AddFeeToOrderObserver implements ObserverInterface {

    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    protected $scopeConfig;
    protected $request;
    protected $checkoutSession;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, checkoutSession $checkoutSession, \Magento\Framework\App\Request\Http $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $quote = $observer->getQuote();
        $CustomFeeFee = $quote->getFee();
        $CustomFeeBaseFee = $quote->getBaseFee();
        if (!$CustomFeeFee || !$CustomFeeBaseFee) {
            return $this;
        }
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('fee', $CustomFeeFee);
        $order->setData('base_fee', $CustomFeeBaseFee);
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $qouteItemId = $orderItem->getQuoteItemId();
            foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
                if ($item->getID() == $qouteItemId) {
                    $logger->info($item->getHandlingCharges() . '_' . $item->getCutToLengthCharges());
                    $orderItem->setHandlingCharges($item->getHandlingCharges());
                    $orderItem->setCutToLengthCharges($item->getCutToLengthCharges());
                }
            }
        }
        return $this;
    }
}
