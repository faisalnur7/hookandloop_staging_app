<?php
namespace Ravedigital\Custom\Observer;

class UpdataCart implements \Magento\Framework\Event\ObserverInterface
{
    protected $_cart;
    protected $_product;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->_cart = $cart;
        $this->_product = $product;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $itemsVisible = $this->_cart->getQuote()->getAllVisibleItems();
        $totalqty = 0;
        foreach ($itemsVisible as $item) {
            if ($item->getProductType() == 'configurable') {
                $options = $item->getOptionByCode('simple_product')->getData();
                $productId = $options["product_id"];
                $child = $this->_product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            } else {
                $productId = $item->getProductId();
                $child = $this->_product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            }
        }
        $this->_cart->getQuote()->setOriginalItemsQty($totalqty)->save();
    }
}
