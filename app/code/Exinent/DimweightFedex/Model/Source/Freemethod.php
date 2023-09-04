<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Exinent\DimweightFedex\Model\Source;

/**
 * Fedex freemethod source implementation
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Freemethod extends \Exinent\DimweightFedex\Model\Source\Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, ['value' => '', 'label' => __('None')]);
        return $arr;
    }
}
