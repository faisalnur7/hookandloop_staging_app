<?php

namespace Sivajik34\CustomFee\Helper;

use Magento\SalesRule\Model\Validator;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\SalesRule\Model\Rule;

class Data extends AbstractHelper {

    /**
     * Custom fee config path
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'customfee/customfee/status';
    const CONFIG_CUSTOM_FEE = 'customfee/customfee/customfee_amount';
    const CONFIG_FEE_LABEL = 'customfee/customfee/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'customfee/customfee/minimum_order_amount';

    protected $validator;
    protected $scopeConfig;
    protected $checkoutSession;
    protected $feeamount;
    protected $rule;

    public function __construct(Validator $ruleValidator, ScopeConfigInterface $scopeConfig, checkoutSession $checkoutSession, Rule $rule) {
        $this->validator = $ruleValidator;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->rule = $rule;
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled() {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);
        return $isEnabled;
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getCustomFee() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/vishal.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $feeamount = 0;
        $cartrule = array();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isQuoteExists = true;
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
            if($item->getProduct()->getMeasurementSoldInSize()){
                $qty = $item->getQty() / $item->getProduct()->getMeasurementSoldInSize();
            } else {
                $qty = $item->getQty();
            }
            $ruleids = explode(',', $item->getAppliedRuleIds() ?? '');

            foreach ($ruleids as $ruleid) {
                if (($ruleid == 25) || ($ruleid == 24 && strpos($optionValue, 'Yes') !== false) || ($ruleid == 23 && strpos($optionValue, 'Yes') !== false)) {
                    $ruleObject = $this->rule->load($ruleid);
                    $action = $ruleObject->getSimpleAction();
                    if ($action == 'by_fixed') {
                        $feeamount += $qty * $ruleObject->getExtraFeeAmount();
                    } elseif ($action == 'by_percent') {
                        $feeamount += ($item->getPrice() * $qty) * ($ruleObject->getExtraFeeAmount() / 100);
                    } elseif ($action == 'cart_fixed') {
                        if (!in_array($ruleid, $cartrule)) {
                            array_push($cartrule, $ruleid);
                            $feeamount += $ruleObject->getExtraFeeAmount();
                        }
                    }
                }
            }
        }
//        $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope);
        return $feeamount;
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getFeeLabel() {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $feeLabel = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, $storeScope);
        $rulename = '';
        $proname = '';
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
            $ruleids = $item->getAppliedRuleIds() ? explode(',', $item->getAppliedRuleIds()):[''];
            foreach ($ruleids as $ruleid) {
                if (($ruleid == 25) || ($ruleid == 24 && strpos($optionValue, 'Yes') !== false) || ($ruleid == 23 && strpos($optionValue, 'Yes') !== false)) {

                    $ruleObject = $this->rule->load($ruleid);
                    $action = $ruleObject->getSimpleAction();
                    if ($action == 'cart_fixed' || $action == 'by_fixed') {
                        if ($rulename == '') {
                            $rulename = $ruleObject->getDescription();
                        } elseif (strpos($rulename, $ruleObject->getDescription()) !== 0) {
                            $rulename = $rulename . ' and ' . $ruleObject->getDescription();
                        }
                        if ($proname == '') {
                            $proname = $item->getName();
                        } elseif (strpos($proname, $item->getName()) !== 0) {
                            $proname = $proname . ',' . $item->getName();
                        }
                    }
                }
            }
        }

        $feeLabel = $rulename . '(' . $proname . ')';
        return $feeLabel;
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount() {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $MinimumOrderAmount = $this->scopeConfig->getValue(self::CONFIG_MINIMUM_ORDER_AMOUNT, $storeScope);
        return $MinimumOrderAmount;
    }
}
