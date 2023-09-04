<?php
namespace Ravedigital\CoreUpdate\Block\Product;

class ConfigurableOption extends \Tigren\Ajaxcart\Block\Product\ConfigurableOption
{

    protected $_registry;
    protected $_cart;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_cart = $cart;
        parent::__construct($context, [] , $data);
    }

    public function getCustomOptionValue($productid)
    {
        $items = $this->_cart->getQuote()->getAllItems();
        $optionData ='';

        foreach ($items as $item) {
            if ($item->getProduct()->getId() == $productid) {
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                if (isset($options) && !empty($options['options'])) {
                    $customOptions = $options['options'];
                    if (!empty($customOptions)) {
                        $optionData ='';
                        foreach ($customOptions as $option) {
                            if (!empty($option['value']) && strpos($option['value'], 'Yes') !== false) {
                                 $radioValue= ': Yes';
                            } else {
                                $radioValue = ' '.$option['value'];
                            }
                            $optionData .= '<span class="option-label">'.$option['label'].'<span class="option-value">'.$radioValue.'</span></span>';
                        }
                    } else {
                        $optionData ='';
                    }
                }
            }
        }
        return $optionData;
    }

    public function getCustomOptions()
    {
        $valid = true;
        if ((null !== $this->_request->getParam('options')) && (count(array_filter($this->_request->getParam('options'))) == 0)) {
            $valid = false;
        }
        return $valid;
    }

    public function getBothOptionConfigsku()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributes = $this->_request->getParam('super_attribute');
        $productid = $this->_request->getParam('product');
        $items = $this->_cart->getQuote()->getAllItems();
        $remote = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        $ip = $remote->getRemoteAddress();
        foreach ($items as $_item) {
            if ($_item->getProduct()->getId() == $productid && $_item->getProductType() == 'configurable') {
                 $child_product_sku=[];
                 $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
                //$child_product_sku[] = $_item->getSku();
                 $product_attributes =[];
                 $productcollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                    ->addAttributeToSelect('*');
                foreach ($attributes as $key => $attribute) {
                    $attrcode = $product->getResource()->getAttribute($key)->getAttributeCode();
                    $attr = $product->getResource()->getAttribute($attrcode);
                    $attrvalue = $attr->getSource()->getOptionText($attribute);
                    
                    if ($attr->usesSource()) {
                         
                        if ($attrcode === 'hook_loop') {
                            $productcollection->addAttributeToFilter([
                                [
                                    'attribute' => $attrcode,
                                    'eq' => '180'],
                                [
                                    'attribute' => $attrcode,
                                    'eq' =>'181' ],
                            ]);
                        } else {
                            $productcollection->addAttributeToFilter($attrcode, ['eq' => $attribute]);
                        }

                    }
                }

                 $productcollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                $productcollection->joinTable('catalog_product_relation', 'child_id=entity_id', [
                            'parent_id' => 'parent_id'
                ], null, 'left')
                ->addAttributeToFilter([['attribute' => 'parent_id', 'eq' => $productid]]);

                if ($productcollection->getData()) {
                    foreach ($productcollection->getData() as $key => $value) {
                        $child_product_sku[] = $value['sku'];
                    }
                }
            }
        }


        return array_unique($child_product_sku);
    }
}
