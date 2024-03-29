<?php

namespace Anshu\SCdata\Block;

use Magento\Catalog\Model\ProductFactory;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

/**
 * Class LeftQty
 * @package Anshu\SCdata\Block
 */
class LeftQty extends \Magento\Framework\View\Element\Template
{
    /**
     * @var GetProductSalableQtyInterface
     */
    protected $salebleqty;

    /**
     * @var StockResolverInterface
     */
    protected $stockresolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storemanager;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $product;

    protected $stockRegistry;

    /**
     * LeftQty constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param ProductFactory $product
     * @param StoreManagerInterface $storemanager
     * @param GetProductSalableQtyInterface $salebleqty
     * @param StockResolverInterface $stockresolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        ProductFactory $product,
        StoreManagerInterface $storemanager,
        GetProductSalableQtyInterface $salebleqty,
        StockResolverInterface $stockresolver,
        array $data = [])
    {
        $this->request = $request;
        $this->product = $product;
        $this->storemanager = $storemanager;
        $this->salebleqty = $salebleqty;
        $this->stockresolver = $stockresolver;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saleble()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/getqty.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            
        $productId = $this->request->getParam('id');
        $websiteCode = $this->storemanager->getWebsite()->getCode();
        $stockDetails = $this->stockresolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = $stockDetails->getStockId();
        $productDetails = $this->product->create()->load($productId);
        $sku = $productDetails->getSku();
        $proType = $productDetails->getTypeId();
        $measize = $productDetails->getMeasurementSoldInSize();
        $finalqty = $this->stockRegistry->getStockItem($productId)->getManageStock();
       
        if ($finalqty == 1 && $proType != 'configurable' && $proType != 'bundle' && $proType != 'grouped') {
            $stockQty = $this->salebleqty->execute($sku, $stockId);
            $stockQty = $this->salebleqty->execute($sku, $stockId);
            // $finalstockQty = $stockQty * $measize ;
            $finalstockQty = $stockQty ;
            // $finalstockQty = $finalstockQty + $stockQty ;
            return $finalstockQty;
            echo($finalstockQty);
        } else {
            return '';
        }

    }
}