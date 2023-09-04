<?php

namespace Sivajik34\CustomFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\SalesRule\Model\Rule;

class AddExtraFeeToOrderObserver implements ObserverInterface {

    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    protected $scopeConfig;
    protected $request;
    protected $_productRepository;
    protected $checkoutSession;
    protected $rule;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, Rule $rule, \Magento\Catalog\Model\ProductRepository $productRepository, checkoutSession $checkoutSession, \Magento\Framework\App\Request\Http $request
    ) {
        $this->_productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->rule = $rule;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $cartrule = [];
        foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $cat = $item->getProduct()->getCategoryIds();
            $optionValue = '';
            if (array_key_exists('options', $options)) {
                $customOptions = $options['options'];
                if (!empty($customOptions)) {
                    foreach ($customOptions as $option) {
                        $optionTitle = $option['label'];
                        if (strpos($optionTitle, 'Would you like this product cut to length') !== false) {
                            $optionValue = $option['value'];
                        }
                    }
                }
            }
            $qty = $item->getQty();
            $ruleAmount = 0;

            $ruleids = $item->getAppliedRuleIds() ? explode(',', $item->getAppliedRuleIds()) :[''];
            // $ruleids = explode(',', $item->getAppliedRuleIds());
            $simplepro = $this->_productRepository->get($item->getProduct()->getSku());
            $qty = $qty / $simplepro->getMeasurementSoldInSize();
            foreach ($ruleids as $ruleid) {
                if (($ruleid == 25) || ($ruleid == 24 && strpos($optionValue, 'Yes') !== false) || ($ruleid == 23 && strpos($optionValue, 'Yes') !== false)) {
                    $ruleObject = $this->rule->load($ruleid);
                    $action = $ruleObject->getSimpleAction();
                    if ($action == 'cart_fixed') {
                        $ruleAmount = $ruleObject->getExtraFeeAmount();
                    } else {
                        $ruleAmount = $qty * $ruleObject->getExtraFeeAmount();
                    }
                    if ($ruleObject->getId() == '25') {
                        if (!in_array($ruleid, $cartrule)) {
                            array_push($cartrule, $ruleid);
                            $item->setHandlingCharges($ruleAmount);
                            $item->setCutToLengthCharges('0');
                        }
                    } elseif ($ruleObject->getId() == '23' || $ruleObject->getId() == '24') {
                        $item->setHandlingCharges('0');
                        $item->setCutToLengthCharges($ruleAmount);
                    }
                }
            }
            $item->save();
        }

        return $this;
    }
}
