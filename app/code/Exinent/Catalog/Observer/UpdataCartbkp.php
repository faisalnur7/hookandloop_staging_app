<?php
namespace Exinent\Catalog\Observer;

class UpdataCart implements \Magento\Framework\Event\ObserverInterface
{
    protected $cartHelper;
    public function __construct(
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        $this->cartHelper = $cartHelper;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/tt.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        // $items = $observer->getCart()->getQuote()->getItems();
        // $info = $observer->getInfo()->getData();
        $item = $observer->getEvent()->getQuoteItem();

    }
}