<?php

namespace Dckap\CustomFields\Model\System\Config\Source;

class UpsServiceOption implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        array $data = []
    ) {
    }

    public function toOptionArray()
    {
        $options = [
           ['value' => 'UPS Ground', 'label' => __('UPS Ground')],
           ['value' => 'UPS Second Day Air', 'label' => __('UPS Second Day Air')],
           ['value' => 'UPS Next Day Air', 'label' => __('UPS Next Day Air')],
           ['value' => 'UPS Standard', 'label' => __('UPS Standard')],
           ['value' => 'UPS Three-Day Select', 'label' => __('UPS Three-Day Select')],
           ['value' => 'UPS Next Day Air Early A.M.', 'label' => __('UPS Next Day Air Early A.M.')],
           ['value' => 'UPS Worldwide Express Plus', 'label' => __('UPS Worldwide Express Plus')],
           ['value' => 'UPS Second Day Air A.M', 'label' => __('UPS Second Day Air A.M')],
           ['value' => 'UPS Worldwide Saver', 'label' => __('UPS Worldwide Saver')],
           ['value' => 'UPS Worldwide Express', 'label' => __('UPS Worldwide Express')],
           ['value' => 'UPS Worldwide Expedited', 'label' => __('UPS Worldwide Expedited')]
        ];
        return $options;
    }
}
