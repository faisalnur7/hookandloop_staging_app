<?php
/**
 * Exinent_Catalog Module
 *
 * @category    catalog
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $_registry;
    const XML_CONFIG_PATH = 'catalog/free_shipping/free_shipping_enable';
    public function __construct(Context $context, \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory, \Magento\Framework\Serialize\Serializer\Json $serialize, \Magento\Framework\Registry $registry)
    {

        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->serialize = $serialize;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    public function getProductDiscounts($product)
    {
        $discounts = array();
        $productCategoryIds = $product->getCategoryIds();
        $collection = $this
            ->ruleCollectionFactory
            ->create();
        $collection->addFieldToFilter('coupon_type', 1);
        $collection->addFieldToFilter('is_active', 1);
        $collection->addFieldToFilter('conditions_serialized', array(
            'like' => '%category_ids%'
        ));
        $myIP = '103.103.214.124';
        foreach ($collection as $discount)
        {
            $cond = $this->serialize->unserialize($discount->getConditionsSerialized());
            $cond = isset($cond['conditions'][0]['conditions']) ? $cond['conditions'][0]['conditions'] : array();
            if (count($cond))
            {
                $categoryIds = array();
                $qty = 0;
                foreach ($cond as $entity)
                {
                    if (isset($entity['attribute']) && ($entity['attribute'] == 'category_ids'))
                    {
                        $categoryIds = array_map('intval', explode(',', $entity['value']));
                    }
                    else if (isset($entity['conditions']))
                    {
                        $newCond = $entity['conditions'];

                        foreach ($newCond as $newEntity)
                        {
                            if (isset($newEntity['attribute']) && ($newEntity['attribute'] == 'category_ids'))
                            {
                                $categoryIds = array_map('intval', explode(',', $newEntity['value']));
                            }

                            if (isset($newEntity['attribute']) && ($newEntity['attribute'] == 'quote_item_qty'))
                            {

                                if (!$qty || ($qty > $newEntity['value']))
                                {
                                    $qty = (int)$newEntity['value'];
                                }
                            }
                        }
                    }

                    if (isset($entity['attribute']) && ($entity['attribute'] == 'quote_item_qty'))
                    {

                        if (!$qty || ($qty > $entity['value']))
                        {
                            $qty = (int)$entity['value'];
                        }
                    }
                }

                if (count(array_intersect($productCategoryIds, $categoryIds)))
                {
                    $unit = 'ROLLS';
                    $name = $discount->getName();
                    $myIP = '103.103.214.124';
                    
                    $pos = stripos($product->getMeasurementSoldInUnit() , strtolower($unit));
                    if ($pos === false)
                    {
                        $unit = strtoupper($product->getBaseUnit());
                    }
                    if ($qty != 0)
                    {
                        // if ($_SERVER['REMOTE_ADDR'] == $myIP)
                        // {
                        //     echo $product->getBaseUnit().'=='.$product->getMeasurementSoldInUnit().'<br>';
                        //     echo 'condition - '.preg_match('/' . $product->getBaseUnit() . 's/', $product->getMeasurementSoldInUnit()).'<br>';
                        // }
                        $qtyDiscount = (int)ceil(($qty / $product->getMeasurementSoldInSize()));
                        if (preg_match('/' . $product->getBaseUnit() . '/', $product->getMeasurementSoldInUnit()) || preg_match('/' . $product->getBaseUnit() . '/', 'Yards'))
                        {
                            if (preg_match('/Wide Loop/', $name))
                            {
                                $unit = 'YARDS';
                                $qtyDiscount = (int)($qty);
                            }
                            elseif (preg_match('/ONE-WRAP Cable Ties/', $name))
                            {
                                $unit = 'STRAPS';
                            }
                            elseif (preg_match('/Unit Volume/', $name))
                            {
                                $unit = 'ROLLS';
                            }
                            elseif (preg_match('/Yard Volume/', $name))
                            {
                                $unit = 'ROLLS';
                                $unit = ($product->getMeasurementSoldInSize() === '1') ? strtoupper($product->getBaseUnit()) : $unit;
                            }
                           
                        }
                        else
                        {
                            continue;
                        }
                        
                        $prodDiscount['discount'] = ((int)$discount->getDiscountAmount());
                        // Add the condition to check if the discount is equal to 0
                        if ($prodDiscount['discount'] !== 0) {
                            $prodDiscount['unit'] = $unit;
                            $prodDiscount['qty'] = $qtyDiscount;
                            $discounts[] = $prodDiscount;
                        }
                    } else if(preg_match('/FULL SPOOL/', $name)){ 
                        $prodDiscount['unit'] = 'FULL SPOOL';
                        $prodDiscount['qty'] = '';
                        $prodDiscount['discount'] = ((int)$discount->getDiscountAmount());
                        $discounts[] = $prodDiscount;
                    }
                }
            }
        }
        
        return array_reverse($discounts);
    }

    public function getFreeShippingApplied($product)
    {
        $rule_name_array = array('Free Shipping Option $50+','Free Shipping Option $200+','Free Shipping Wide Loop 5 Yards+');
        $applied = 0;
        $todayDate = date("Y-m-d");
        // $rule_name = 'Free Shipping Option $200+';
        $productCategoryIds = $product->getCategoryIds();
        $collection = $this->ruleCollectionFactory->create();
        $collection->addFieldToFilter('coupon_type', 1);
       // $collection->addFieldToFilter('from_date', array(array('gteq' => $todayDate), array('null' => true)));//Make these changes
        $collection->addFieldToFilter('to_date', array(array('gteq' => $todayDate), array('null' => true)));//Make these changes
        $collection->addFieldToFilter('is_active', 1);
        /*$collection->addFieldToFilter('name', array(
            array(
                'like' => '%' . $rule_name . '%'
            )
        ));*/
        $collection->addFieldToFilter('name', ['in' => $rule_name_array]);
       $categoryIds = array();
        foreach ($collection as $key => $discount)
        {
            $cond = $this->serialize->unserialize($discount->getActionsSerialized());
            if (count($cond['conditions']))
            {   foreach ($cond['conditions'] as $entity)
                {   if (isset($entity['attribute']) && ($entity['attribute'] == 'category_ids'))
                    {   array_push($categoryIds, $entity['value']);
                        //$categoryIds = array_map('intval', explode(',', $entity['value']));
                    }
                }
            }
        }
        if (count(array_intersect($productCategoryIds, $categoryIds)))
        {
            $applied = 1;
        }
        // $allowed_category = 43;
        // if(in_array($allowed_category,$productCategoryIds)){
        //     $applied =1;
        // }
        return $applied;

    }

    public function getCurrentProduct()
    {
        return $this
            ->_registry
            ->registry('current_product');
    }

    public function getConfig()
    {
        $configValue = $this->scopeConfig->getValue(self::XML_CONFIG_PATH, ScopeInterface::SCOPE_STORE); // For Store
        return $configValue;
    }
    
      public function getCatalogConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
