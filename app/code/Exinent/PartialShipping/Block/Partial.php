<?php

namespace Exinent\PartialShipping\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockStateInterface;

class Partial extends Template {

    protected $checkoutSession;
    protected $productFactory;
    protected $stockInterface;

    public function __construct(Context $context, Session $checkoutSession, ProductFactory $product, StockStateInterface $stockItem) {
        $this->checkoutSession = $checkoutSession;
        $this->productFactory = $product;
        $this->stockInterface = $stockItem;
        return parent::__construct($context);
    }

    public function getBackorder() {
        $strapCountArray = array();
        $leadArray = array();
        $leadArraynew = array();
        $strapcount;
        $quote = $this->checkoutSession->getQuote()->getAllItems();

        foreach ($quote as $item) {
            if ($item->getProductType() == 'configurable') {
                $options = $item->getOptionByCode('simple_product')->getData();
                $productId = $options["product_id"];
                $child = $this->productFactory->create()->load($productId);
                $leadTime = $child->getData('backorder_lead_time');
                $productName = $child->getData('name');
                if (preg_match('/Straps/', $productName) && !(preg_match('/ONE-WRAP® Strap/', $productName))) {
                    $strapcount = 1;
                    array_push($strapCountArray, $strapcount);
                } else {
                    $strapcount = 0;
                    array_push($strapCountArray, $strapcount);
                }
                $productQuantity = $this->stockInterface->getStockQty($productId);
                $categoryId = $child->getCategoryIds();
                if (in_array("8", $categoryId)) {
                    $strapProduct = true;
                }
            } else {
                $productId = $item->getProductId();
                $product = $this->productFactory->create()->load($productId);
                $productQuantity = $this->stockInterface->getStockQty($productId);
                $leadTime = $product->getData('backorder_lead_time');
                if (empty($leadTime)) {
                    $leadTime = $product->getResource()->getAttribute('backorder_lead_time')->getDefaultValue();
                }
                $productName = $product->getData('name');
                if (preg_match('/Straps/', $productName) && !(preg_match('/ONE-WRAP® Strap/', $productName))) {
                    $strapcount = 1;
                    array_push($strapCountArray, $strapcount);
                } else {
                    $strapcount = 0;
                    array_push($strapCountArray, $strapcount);
                }
            }
            $qty = round($productQuantity);
            if (strpos($qty, '-') !== false) {
                $qty = 0;
            }
            if ($qty < $item->getQty() || $qty < 0) {
                array_push($leadArray, $leadTime);
            }
        }

        if (count($leadArray) > 0) {
            foreach ($leadArray as $value) {
                if ($value && strpos($value, 'weeks') !== false || $value && strpos($value, 'Week') !== false) {
                    $data = explode(',', $value ?? '');
                    $weeks = max($data);
                    $weeks = preg_replace('/[^0-9]/', '', $weeks);
                    $value = $weeks * 7;
                } else {
                    $value = $value;
                }
                $data = explode(',', $value ?? '');
                foreach ($data as $datavalue) {
                    $str = preg_replace('/[^0-9]/', '', $datavalue);
                    array_push($leadArraynew, $str);
                }
            }
            $maxTime = max($leadArraynew);
            if (!empty($maxTime)) {
                if ($maxTime >= 7) {
                    $maxTime = ($maxTime / 7) . ' weeks';
                } else {
                    $maxTime = $maxTime . ' business days';
                }
            }
            if (in_array("0", $strapCountArray) && !empty($maxTime)) {
                return $maxTime;
            }
        }
    }
}