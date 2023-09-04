<?php
namespace Ravedigital\Custom\Observer;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $quoteFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId= $order->getQuoteId();

        $quote = $this->quoteFactory->create()->load($quoteId);
        $taxreliefCode = $quote->getPayment()->getTaxReliefCode();
        $taxState = $quote->getPayment()->getTaxReliefState();

        $orderPayment = $order->getPayment();

        if (!$orderPayment->getTaxReliefCode() && !$orderPayment->getTaxReliefState()) {
            $orderPayment->setTaxReliefCode($taxreliefCode);
            $orderPayment->setTaxReliefState($taxState);
        }
    }
}
