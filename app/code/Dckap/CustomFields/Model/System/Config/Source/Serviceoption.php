<?php

namespace Dckap\CustomFields\Model\System\Config\Source;

class Serviceoption implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        array $data = []
    ) {
    }

    public function toOptionArray()
    {
        $options = [
           ['value' => 'Ground', 'label' => __('Ground')],
           ['value' => '2-Day', 'label' => __('2-Day')],
           ['value' => 'Overnight', 'label' => __('Overnight')],
           ['value' => 'DHL Packet International', 'label' => __('DHL Packet International')],
           ['value' => 'DHL Packet Plus International', 'label' => __('DHL Packet Plus International')],
           ['value' => 'DHL Parcel International Direct', 'label' => __('DHL Parcel International Direct')]
        ];
        return $options;
    }
}
