<?php

namespace Anshu\SCdata\Block\ConfigurableProduct\Product\View\Type;

use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Catalog\Model\Product;


class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $_productRepository;
    protected $_custompricehelper;
    protected $stockRegistry;
    protected $salebleqty;
    protected $product;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Ravedigital\Showprice\Helper\CustomPriceHelper $customPriceHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,

        GetProductSalableQtyInterface $salebleqty,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        Product $product
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_custompricehelper = $customPriceHelper;
        $this->_productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->salebleqty = $salebleqty;
        $this->product = $product;
        
    //$this->_attributes = $attributes;
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    )
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/qty.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            



        $sname = [];
        $sdescription = [];
        $ssku = [];
        $sadditionaldata = [];
        $sshortdescription = [];
        $measurementsold=[];
        $measurementSoldInUnit = [];
        $finalprice=[];
        $surl = [];
        $oldprice=[];
        $sqty =[];
        $reaminingqty =[];
        $sminqty=[];
        $sleadtime = [];
        $backorderleadtime=[];
        $measize = [];
        $scqty =[];
        $finalstockQty = [];
        $managestock = [];
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);

        foreach ($subject->getAllowProducts() as $prod) {
            $id = $prod->getId();
            $product = $this->getProductById($id);
            $sname[$id] = $product->getName();
            $sdescription[$id] = $product->getDescription();
            $sshortdescription[$id] = $product->getShortDescription();
            $surl[$id] = $product->getProductUrl();
            $ssku[$id] = $product->getSku();
            $stockItem = $product->getExtensionAttributes()->getStockItem();
            $sqty[$id] = $stockItem->getQty();
            $scqty[$id] = $stockItem->getQty();
            $sminqty[$id] = $stockItem->getMinSaleQty();
            $sleadtime[$id] = $product->getBackorderLeadTime();
            $measurementsold[$id] = $product->getBaseUnit();

            $reaminingqty[$id] = $this->stockRegistry->getStockItem($product->getId())->getQty();
            $special_price = $this->_custompricehelper->getSpecialPrice($product->getId());
            if($special_price){
                //$finalprice[$id] = $product->getSpecialPrice();
                $finalprice[$id] = $this->_custompricehelper->getSpecialPrice($product->getId());
                $oldprice[$id] = $product->getPrice();
            } else {
                $finalprice[$id] = $product->getPrice();
                $oldprice[$id] = '';
            }
            $measize[$id] = $product->getMeasurementSoldInSize();
            $backorderleadtime[$id] = $product->getBackorderLeadTime()?$product->getBackorderLeadTime():'';
            $measurementSoldInUnit[$id] = $product->getMeasurementSoldInUnit();
            // $finalstockQty[$id] = $scqty[$id] * $measize[$id] / 100 ;
            // $finalstockQty[$id] = $scqty[$id] * $measize[$id] ;
            $finalstockQty[$id] = $scqty[$id] ;
            // $finalstockQty[$id] = $finalstockQty[$id] + $scqty[$id] ;
            $managestock[$id] = $this->stockRegistry->getStockItem($product->getId())->getManageStock();
        }
        $config['sname'] = $sname;
        $config['sdescription'] = $sdescription;
        $config['ssku'] = $ssku;
        $config['sshortdescription'] = $sshortdescription;
        $config['surl'] = $surl;
        $config['sqty'] = $sqty;
        // $logger->info(json_encode($sqty));
        // $logger->info('shubham');
        $config['scqty'] = $scqty;
        $config['reaminingqty'] = $reaminingqty;
        $config['sminqty'] = $sminqty;
        $config['sleadtime'] = $sleadtime;
        $config['measurementsold'] = $measurementsold;
        $config['finalprice'] = $finalprice;
        $config['oldprice'] = $oldprice;
        $config['measize'] = $measize;
        $config['backorderleadtime'] = $backorderleadtime;
        $config['measurementSoldInUnit'] = $measurementSoldInUnit;
        $config['finalstockQty'] = $finalstockQty;
        $config['managestock'] = $managestock;
        return $this->jsonEncoder->encode($config);
    }
}
