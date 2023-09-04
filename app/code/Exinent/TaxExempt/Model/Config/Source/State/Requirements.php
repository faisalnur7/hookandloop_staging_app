<?php

namespace Exinent\TaxExempt\Model\Config\Source\State;

class Requirements implements \Magento\Framework\Option\ArrayInterface 
{

    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label'=> __('Not required')],
            ['value' => 1, 'label'=> __('Required')],
            ['value' => 2, 'label'=> __('Per design')]   
        ];
    }

}
