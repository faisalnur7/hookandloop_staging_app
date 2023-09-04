<?php

/*
 * @category   AmazonProductMapping
 * @author     pawan 
 * @copyright  Exinent_AmazonProductMapping
 * 
 */

namespace Exinent\Catalog\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductUpdateAmazonQuantity implements ObserverInterface{

    protected $_logger;
    protected $amazonFactory;

    /** @var  \Magento\Catalog\Model\ProductFactory */
    protected $productFactory;

    /** @var  \Magento\Catalog\Model\ResourceModel\Product */
    protected $productResourceModel;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, 
        \Magento\Framework\Message\Manager $messageManager, 
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem
    ) {
        $this->_logger = $logger;
        $this->timezone = $timezone;
         $this->productFactory = $productFactory;
        $this->productResourceModel = $productResourceModel;
        $this->stockItem = $stockItem;
        
        //parent::__construct($helperFactory, $activeRecordFactory, $modelFactory);
    }
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/productupdateamazonqty.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
//        $logger->info("here comming rey");
        $item = $observer->getEvent()->getItem();
        $qty = $observer->getEvent()->getQty();
        $product = $this->productFactory->create()->setStoreId(1)->load($item->getProductId());
        $sku = $product->getData('sku');
        $duplicateProductCollection = '';
//        $this->productResourceModel->load($product, $item->getProductId());
//        $product->setStoreId($storeId);
//        $product->setPrice($price);
        $duplicateProducts = array("163827-dis", "187038-3610", "181710-3610", "184053-3610", "185714-3610", "184428-3610", "197214-3610", "195867-3610", "182815-3610", "181214-3610", "VEL-120930", "sim-191195", "sim-184988", "VEL-184987", "aim-184989", "sim-189453", "sim-DG10WHLA", "sim-DG15WHHA", "sim-DG20BLLS", "sim-190660", "sim-120382", "amz-DG20WHLPLY", "DG-DG20BLHS", "amn-190213", "sim-192318", "amz-DG20WHHPLY", '184987');
        foreach ($duplicateProducts as $duplicateProduct) {
            if (strpos($duplicateProduct, $sku) !== false) {
                $duplictaeProductSku = $duplicateProduct;
                $duplicateProductCollection = $this->productFactory->create()->setStoreId(1)->loadByAttribute('sku', $duplictaeProductSku);
                ;
                break;
            }
        }

        if ($product->dataHasChangedFor('measurement_sold_in_size') || ($item->getQty() != $item->getOrigData('qty'))) {
            $amazonQuantity = round($item->getData('qty') / $product->getMeasurementSoldInSize());
            $product->setData('amazon_qty', $amazonQuantity);
            $product->getResource()->saveAttribute($product, 'amazon_qty');
            if ($duplicateProductCollection) {
                $duplicateProductQuantity = $this->stockItem->getStockQty($duplicateProductCollection->getId(), $product->getStore()->getWebsiteId());



                $duplicateProductQuantity->setQty($item->getData('qty'));
                $duplicateProductQuantity->save();

                $duplicateProductCollection->setData('amazon_qty', $amazonQuantity);
                $duplicateProductCollection->getResource()->saveAttribute($duplicateProductCollection, 'amazon_qty');
            }
        }
//        $this->productResourceModel->saveAttribute($product, 'price');
    }

}