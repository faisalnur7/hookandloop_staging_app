<?php

/**
 * Exinent_Catalog Module 
 *
 * @category    catalog
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Model\Product\Attribute\Backend;

class Price extends \Magento\Catalog\Model\Product\Attribute\Backend\Price {

    public function afterSave($object) {

        if (!empty($object->getData("price")) && !empty($object->getData("measurement_sold_in_size"))) {
            $sortPrice = ($object->getData("price") * $object->getData("measurement_sold_in_size"));
            $object->setData('sort_price', $sortPrice);
            $object->getResource()->saveAttribute($object, 'sort_price');
        }
        /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $this->getAttribute();
        $attributeCode = $attribute->getAttributeCode();
        $value = $object->getData($attributeCode);
        // $value may be passed as null to unset the attribute
        if ($value === null || (float) $value > 0) {
            if ($attribute->isScopeWebsite() && $object->getStoreId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                if ($this->isUseDefault($object)) {
                    $value = null;
                }
                foreach ((array) $object->getWebsiteStoreIds() as $storeId) {
                    $object->addAttributeUpdate($attributeCode, $value, $storeId);
                }
            }
        }

        return $this;
    }

}
