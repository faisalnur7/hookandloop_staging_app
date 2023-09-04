<?php

namespace Ravedigital\Showprice\Plugin\Block\Product;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ListProduct
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProductBlock;

    /**
     * @var Configurable
     */
    protected $configurableProduct;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Ravedigital\Showprice\Helper\CustomPriceHelper
     */
    protected $showpricehelper;

    /**
     * ListProduct constructor.
     *
     * @param Configurable $configurableProduct
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        Configurable $configurableProduct,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Ravedigital\Showprice\Helper\CustomPriceHelper $showpricehelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->pricingHelper = $pricingHelper;
        $this->showpricehelper = $showpricehelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        /*if (Configurable::TYPE_CODE !== $product->getTypeId()) {
            return $proceed($product);
        }*/

        $this->listProductBlock = $subject;
        $priceText = $this->getPriceRange($product);

        return $priceText;
    }

    /**
     * Get configurable product price range
     *
     * @param $product
     * @return string
     */
      public function getPriceRange($product)
    {
        $childProductPrice = [];
        $measurement_sold_in_size = 1;
        $max = 1;
        $min = 1;
        $special_price =0;
        if($product->getHidePriceCategory()){
            return;
        }
        if($product->getTypeId() === 'configurable'){
            $childProducts = $this->configurableProduct->getUsedProducts($product);
            foreach($childProducts as $child) {
                $price = number_format($child->getPrice(), 2, '.', '');
                $finalPrice = number_format($child->getFinalPrice(), 2, '.', '');
                $measurement_sold_in_size =  $child->getMeasurementSoldInSize();
                if($price == $finalPrice) {
                    $childProductPrice[] = $price * $measurement_sold_in_size;
                } else if($finalPrice < $price) {
                    $childProductPrice[] = $finalPrice * $measurement_sold_in_size;;
                }
            }
            $max = $this->pricingHelper->currencyByStore(max($childProductPrice));
            $min = $this->pricingHelper->currencyByStore(min($childProductPrice));
            
        } else {
            if($this->showpricehelper->getSpecialPrice($product->getId())){
                $special_price = $product->getSpecialPrice();
                $special_price = $this->pricingHelper->currencyByStore($special_price*$product->getMeasurementSoldInSize());
            }
            $price = $product->getPrice();
            $max = $min = $this->pricingHelper->currencyByStore($price*$product->getMeasurementSoldInSize());
        }
        if($min==$max){
            //return $this->getPriceRender($product, "$min", '',$special_price);
            return $this->getPriceRender($product, "$min",$special_price,'');
        } else {

            //return $this->getPriceRender($product, "$min-$max", '',$special_price);
            return $this->getPriceRender($product, "$min-$max",$special_price,'');
        }
    }


    /**
     * Price renderer
     *
     * @param $product
     * @param $price
     * @return mixed
     */
    protected function getPriceRender($product, $price,$special_price, $text='')
    {
        return $this->listProductBlock->getLayout()->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Ravedigital_Showprice::product/price/range/price.phtml')
            ->setData('price_id', 'product-price-'.$product->getId())
            ->setData('display_label', $text)
            ->setData('special_price',$special_price)
            ->setData('product_id', $product->getId())
            ->setData('display_value', $price)->toHtml();
    }
}
