<?php

namespace Dckap\CustomFields\Model\System\Config\Source;

class Methodoption implements \Magento\Framework\Option\ArrayInterface
{

    public function __construct(
        array $data = []
    ) {
    }

    public function toOptionArray()
    {
        $options = [
           ['value' => 'Fedex', 'label' => __('Fedex')],
           ['value' => 'UPS', 'label' => __('UPS')],
           ['value' => 'DHL', 'label' => __('DHL')]
        ];
        return $options;
    }
}
