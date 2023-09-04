<?php
namespace Ravedigital\Showprice\Block\Product;
use Magento\Framework\View\Element\Template;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ListProduct extends Template
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
        \Magento\Framework\View\Element\Template\Context $context,
        Configurable $configurableProduct,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Ravedigital\Showprice\Helper\CustomPriceHelper $showpricehelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->pricingHelper = $pricingHelper;
        $this->logger = $logger;
        $this->showpricehelper = $showpricehelper;
        return parent::__construct($context);
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
        if($product->getTypeId() === 'configurable'){
            $childProducts = $this->configurableProduct->getUsedProducts($product);
            foreach($childProducts as $child) {
                $price = number_format($child->getPrice(), 2, '.', '');
                $finalPrice = number_format($child->getFinalPrice(), 2, '.', '');
                $measurement_sold_in_size =  $child->getMeasurementSoldInSize();
                if($price == $finalPrice) {
                    $childProductPrice[] = $price * $measurement_sold_in_size;
                }else if($finalPrice < $price) {
                    $childProductPrice[] = $finalPrice * $measurement_sold_in_size;
                }
            }
            $max = $this->pricingHelper->currencyByStore(max($childProductPrice));
            $min = $this->pricingHelper->currencyByStore(min($childProductPrice));
            
        } else {
            if($this->showpricehelper->getSpecialPrice($product->getId())){
                //$special_price = $product->getSpecialPrice();
                $special_price = $this->showpricehelper->getSpecialPrice($product->getId());
                $special_price = $special_price*$product->getMeasurementSoldInSize();
            }
            $price = $product->getPrice();
            
            $max = $min = $price*$product->getMeasurementSoldInSize();
            
        }
        if($min==$max){
            return $this->getPriceRender($product, "$min",$special_price,'');
        } else {
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
    protected function getPriceRender($product, $price,$special_price,$text='')
    {
        $data['price_id'] =  'product-price-'.$product->getId();
        $data['display_label'] = $text;
        $data['special_price'] = $special_price;
        $data['product_id'] = $product->getId();
        $data['display_value'] = $price;
        
        return $data;
    }
}
