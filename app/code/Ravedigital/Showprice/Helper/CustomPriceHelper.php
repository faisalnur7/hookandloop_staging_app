<?php
namespace Ravedigital\Showprice\Helper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class CustomPriceHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
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
    protected $productFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Configurable $configurableProduct,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->pricingHelper = $pricingHelper;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        parent::__construct($context);
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
            if($this->getSpecialPrice($product->getId())!=0){
                $special_price = $product->getSpecialPrice();
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

    public function getSpecialPrice($productId){
        $product = $this->productFactory->create()->load($productId);
        $price = $product->getPrice();
        $orgprice = $product->getPrice();
        $specialPrice = $product->getSpecialPrice();
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();
        $today = time();
        if ($specialPrice < $price) {
          if (
            (is_null($specialFromDate) && is_null($specialToDate)) ||
            ($today >= strtotime($specialFromDate) && is_null($specialToDate)) ||
            ($today <= strtotime($specialToDate) && is_null($specialFromDate)) ||
            ($today >= strtotime($specialFromDate) && $today <= strtotime($specialToDate))
          ) {
            return $specialPrice;
          } 
        }
        return 0; 
    }
}
