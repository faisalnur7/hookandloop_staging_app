<?php

namespace Dckap\CustomFields\Model\System\Config\Source;

class DhlServiceOption implements \Magento\Framework\Option\ArrayInterface
{   
    public function __construct(
        array $data = []
    ) {
    }

    public function toOptionArray()
    {
        $options = [
           ['value' => 'DHL Packet International', 'label' => __('DHL Packet International')],
           ['value' => 'DHL Packet Plus International', 'label' => __('DHL Packet Plus International')],
           ['value' => 'DHL Parcel International Direct', 'label' => __('DHL Parcel International Direct')],
           ['value' => 'Domestic express 12:00', 'label' => __('Domestic express 12:00')],
           ['value' => 'Easy shop', 'label' => __('Easy shop')],
           ['value' => 'Jetline', 'label' => __('Jetline')],
           ['value' => 'Express easy', 'label' => __('Express easy')],
           ['value' => 'Express worldwide', 'label' => __('Express worldwide')],
           ['value' => 'Medical express', 'label' => __('Medical express')],
           ['value' => 'Express 9:00', 'label' => __('Express 9:00')],
           ['value' => 'Freight worldwide', 'label' => __('Freight worldwide')],
           ['value' => 'Economy select', 'label' => __('Economy select')],
           ['value' => 'Jumbo box', 'label' => __('Jumbo box')],
           ['value' => 'Express 10:30', 'label' => __('Express 10:30')],
           ['value' => 'Europack', 'label' => __('Europack')],
           ['value' => 'Express 12:00', 'label' => __('Express 12:00')]
        ];
        return $options;
    }
}
