<?php

namespace Exinent\DimweightFedex\Model\Config\Source;

class Unit implements \Magento\Framework\Option\ArrayInterface 
{

    public function toOptionArray() 
    {
        return [['value' => '', 'label' => __('-- Please Select --')], ['value' => 'in', 'label' => __('Inches')], ['value' => 'cm', 'label' => __('Centimeters')]];
    }

    public function toArray() 
    {
        return [
            '' => '-- Please Select --',
            'in' => 'Inches',
            'cm' => 'Centimeters'
            ];
    }
}
