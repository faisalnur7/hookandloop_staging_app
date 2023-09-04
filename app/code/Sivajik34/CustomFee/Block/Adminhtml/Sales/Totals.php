<?php

namespace Sivajik34\CustomFee\Block\Adminhtml\Sales;

use Magento\SalesRule\Model\Rule;

class Totals extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Sivajik34\CustomFee\Helper\Data
     */
    protected $_dataHelper;
    protected $rule;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Sivajik34\CustomFee\Helper\Data $dataHelper, \Magento\Directory\Model\Currency $currency, Rule $rule, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_currency = $currency;
        $this->rule = $rule;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder() {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource() {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol() {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getFee()) {
            return $this;
        }
        $rulename = '';
        $proname = '';
        foreach ($this->getOrder()->getAllVisibleItems() as $item) {
            $options = $item->getProductOptions();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
            $arrayKeys = ['options', 'additional_options', 'attributes_info'];
            $optionValue = '';
            foreach ($arrayKeys as $key) :
                if (isset($options[$key])) :
                    foreach ($options[$key] as $option) :
                        $optionTitle = $option['label'];
                        if (strpos($optionTitle, 'Would you like this product cut to length') !== false) {
                            $optionValue = $option['value'];
                        }
                    endforeach;
                endif;
            endforeach;
            $ruleids = explode(',', $item->getAppliedRuleIds());
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
//        }
        $feeLabel = $rulename . '(' . $proname . ')';
        $total = new \Magento\Framework\DataObject(
                [
            'code' => 'fee',
            'value' => $this->getSource()->getFee(),
            'label' => $feeLabel,
                ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
