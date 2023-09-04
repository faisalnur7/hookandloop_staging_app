<?php

namespace Ravedigital\Addtocart\Plugin;

class DefaultItemPlugin
{


    protected $_productRepository;
        
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_productRepository = $productRepository;
    }

    public function afterGetItemData(\Magento\Checkout\CustomerData\AbstractItem $subject, $result, \Magento\Quote\Model\Quote\Item $item)
    {
        $data = $this->getProductDetails($result['product_sku']);
        $result['product_name'] = $data['product_name'];
        $result['measurementsoldin'] = $data['measurement_sold_in_size'];
        $result['qty'] = $result['qty']/$data['measurement_sold_in_size'];
        $result['product_price'] = $data['final_price'] * $data['measurement_sold_in_size'];
        $result['product_price_value'] = $result['product_price'];
        return $result;
    }

    public function getProductDetails($product_sku)
    {
        $data = [];
        $product = $this->_productRepository->get($product_sku);
        $data['product_name'] = $product->getName();
        $data['measurement_sold_in_size'] = $product->getMeasurementSoldInSize()?$product->getMeasurementSoldInSize():1;
        $data['final_price'] = $product->getFinalPrice();
        return $data;
    }
}
