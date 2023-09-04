<?php

/**
 * Exinent_Catalog Module 
 *
 * @category    checkout
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class DetectProductChanges implements ObserverInterface {

    const XML_NOTIFY_EMAIL = 'dev/debug/enable';
    const XML_NOTIFY_VALUE = 'dev/debug/emails';

    protected $scopeConfig;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        $old = $product->getOrigData();
        $new = $product->getData();
        $oldmeasurement = $product->getOrigData('measurement_sold_in_size');
        $newmeasurement = $product->getData('measurement_sold_in_size');
        $configValue = $this->scopeConfig->getValue(self::XML_NOTIFY_EMAIL, $storeScope);
        $configEmailList = $this->scopeConfig->getValue(self::XML_NOTIFY_VALUE, $storeScope);

        if ($configValue && !empty($configEmailList) && !empty($old)) {
            if ($product->dataHasChangedFor('measurement_sold_in_size')) {
                $productName = $product->getName();
                $productSku = $product->getSku();
                $productStoreName = $product->getStore()->getName();
                $this->_sendStatusMail($productName, $productSku, $oldmeasurement, $newmeasurement, $productStoreName, $configEmailList);
            }
        }
    }

    private function _sendStatusMail($productName, $productSku, $old, $new, $storeName, $configEmailList) {
        $emails = explode(',', $configEmailList);

        $emailTemplateVariables = array(
            'product_name' => $productName,
            'product_sku' => $productSku,
            'product_old' => $old,
            'product_new' => $new,
            'store_name' => $storeName
        );

        $template = "email_template_product_attribute_update";
        $this->inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($template);
        $this->_transportBuilder->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => 1]);
        $this->_transportBuilder->setTemplateVars($emailTemplateVariables);
//        $this->_transportBuilder->setSubject("Product Attribute Change Detect");;
        $this->_transportBuilder->addTo($emails);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

}
