<?php

namespace Exinent\PriceRange\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Model\Product;
use Ravedigital\Showprice\Helper\CustomPriceHelper;

class Pricerange extends Template {

    protected $price_helper;
    protected $product;
    protected $registry;
    protected $custom_price_helper;

    public function __construct( 
    Product $product, PriceHelper $price_helper, Registry $registry, CustomPriceHelper $custom_price_helper, Context $context, array $data = []
    ) {
        $this->product = $product;
        $this->price_helper = $price_helper;
        $this->registry = $registry;
        $this->custom_price_helper = $custom_price_helper;
        parent::__construct($context, $data);
    }

    public function getConfigdata() {
        $simpleprod = array();
        $price = array();
        $currentProduct = $this->registry->registry('current_product');
        $_children = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct);
        $i=0;
        foreach ($_children as $child) {
            $simpleproductsdata = $this->product->load($child->getId());
            if($simpleproductsdata->getData('measurement_sold_in_size') != ''):$measurementSold = $simpleproductsdata->getData('measurement_sold_in_size');
            else: $measurementSold = 1; 
            endif;
           if ($simpleproductsdata->getData('configuratble_width'))
                $simpleprod[$i]['configuratble_width'] = $simpleproductsdata->getData('configuratble_width');
            if ($simpleproductsdata->getData('color'))
                $simpleprod[$i]['color'] = $simpleproductsdata->getData('color');
            if ($simpleproductsdata->getData('hook_loop'))
                $simpleprod[$i]['hook_loop'] = $simpleproductsdata->getData('hook_loop');
            if ($simpleproductsdata->getData('adhesive'))
                $simpleprod[$i]['adhesive'] = $simpleproductsdata->getData('adhesive');
            if ($simpleproductsdata->getData('size'))
                $simpleprod[$i]['size'] = $simpleproductsdata->getData('size');
            if ($simpleproductsdata->getData('wide_loop_type'))
                $simpleprod[$i]['wide_loop_type'] = $simpleproductsdata->getData('wide_loop_type');
            if ($simpleproductsdata->getData('webbing_weight'))
                $simpleprod[$i]['webbing_weight'] = $simpleproductsdata->getData('webbing_weight');
            if ($simpleproductsdata->getData('ring_shape'))
                $simpleprod[$i]['ring_shape'] = $simpleproductsdata->getData('ring_shape');
            if ($simpleproductsdata->getData('style'))
                $simpleprod[$i]['style'] = $simpleproductsdata->getData('style');
            if ($simpleproductsdata->getData('mva8_type'))
                $simpleprod[$i]['mva8_type'] = $simpleproductsdata->getData('mva8_type');
            if($this->custom_price_helper->getSpecialPrice($simpleproductsdata->getId() ) ){
                $specialprice = $this->custom_price_helper->getSpecialPrice($simpleproductsdata->getId());
                $simpleprod[$i]['price'] = $price[] = $specialprice* $measurementSold;
                $simpleprod[$i]['old_price'] = $simpleproductsdata->getPrice()* $measurementSold;
            } else {
                $simpleprod[$i]['price'] = $price[] = $simpleproductsdata->getPrice()* $measurementSold;
            }
            $i++;
        }
        if(!empty($price)){
            $min_price = min($price);
            $max_price = max($price);
            if ($min_price == $max_price) {
                $result['price'] = $this->price_helper->currency($min_price, true, false);
            } else {
                $result['price'] = $this->price_helper->currency($min_price, true, false) . '-' . $this->price_helper->currency($max_price, true, false);
            }
            $result['min_price'] = $min_price;
            $result['json'] = json_encode($simpleprod);
            return $result;
        }
    }
    public function getProductId(){
        $currentProduct = $this->registry->registry('current_product');
        return $currentProduct->getId();
    }
}
