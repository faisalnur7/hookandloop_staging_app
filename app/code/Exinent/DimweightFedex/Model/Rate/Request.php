<?php

namespace Exinent\DimweightFedex\Model\Rate;

class Request extends \Magento\Quote\Model\Quote\Address\RateRequest {

    public function getPackageWeight() {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get("\Exinent\DimweightFedex\Helper\Data");
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dimweight.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);

        if ($this->getUseDimweightOverride()) {

            $running_billable_weight = 0;

            foreach ($this->getAllItems() as $item) {

                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        if (!$child->getProduct()->isVirtual()) {
                            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($child->getProduct()->getId());
                            $original_weight = $product->getWeight() * $item->getQty() * $child->getQty();
                            $dim_weight = ( $helper->getDimweight($product->getData($helper->getPackageAttribute('length')), $product->getData($helper->getPackageAttribute('width')), $product->getData($helper->getPackageAttribute('height'))) * $item->getQty() * $child->getQty() );
                            $running_billable_weight += max($dim_weight, $original_weight);
                        }
                    }
                } else {
                    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
                    $original_weight = $product->getWeight() * $item->getQty();
                    $dim_weight = ( $helper->getDimweight( $product->getData($helper->getPackageAttribute('length')), $product->getData($helper->getPackageAttribute('width')), $product->getData($helper->getPackageAttribute('height')) ) * $item->getQty() );
                    $running_billable_weight += max($dim_weight, $original_weight);
                    /*--------------------Testing Logger-----------------------------*/
//                    $logger->info($running_billable_weight);
//                    $logger->info('done');
                    /*---------------------------------------------------------------*/
                }
            }

            return $running_billable_weight;
        } else {

            return $this->getData('package_weight');
        }
    }

    public function getFreeMethodWeight() {
        return $this->getPackageWeight();
    }

}
