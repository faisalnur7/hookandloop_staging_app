<?php

namespace Dckap\CustomFields\Model\System\Config\Source;

class FedexServiceOption implements \Magento\Framework\Option\ArrayInterface
{   
    public function __construct(
        array $data = []
    ) {
    }

    public function toOptionArray()
    {
        $options = [
           ['value' => 'Ground', 'label' => __('Ground')],
           ['value' => '2 Day', 'label' => __('2 Day')],
           ['value' => 'Standard Overnight', 'label' => __('Standard Overnight')],
           ['value' => 'Europe First Priority', 'label' => __('Europe First Priority')],
           ['value' => '1 Day Freight', 'label' => __('1 Day Freight')],
           ['value' => '2 Day Freight', 'label' => __('2 Day Freight')],
           ['value' => '2 Day AM', 'label' => __('2 Day AM')],
           ['value' => '3 Day Freight', 'label' => __('3 Day Freight')],
           ['value' => 'Express Saver', 'label' => __('Express Saver')],
           ['value' => 'First Overnight', 'label' => __('First Overnight')],
           ['value' => 'Ground Home Delivery', 'label' => __('Ground Home Delivery')],
           ['value' => 'International Economy', 'label' => __('International Economy')],
           ['value' => 'Intl Economy Freight', 'label' => __('Intl Economy Freight')],
           ['value' => 'International First', 'label' => __('International First')],
           ['value' => 'International Ground', 'label' => __('International Ground')],
           ['value' => 'International Priority', 'label' => __('International Priority')],
           ['value' => 'Intl Priority Freight', 'label' => __('Intl Priority Freight')],
           ['value' => 'Priority Overnight', 'label' => __('Priority Overnight')],
           ['value' => 'Smart Post', 'label' => __('Smart Post')],
           ['value' => 'Freight', 'label' => __('Freight')],
           ['value' => 'National Freight', 'label' => __('National Freight')]
        ];
        return $options;
    }
}
