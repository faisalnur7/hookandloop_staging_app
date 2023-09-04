<?php
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class Type
{
    /**
     * Gateway types
     */
    const EMAIL = 'email';

    const DEFAULT_TYPE = self::EMAIL;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::EMAIL,  'label' => __('Email Gateway')]
        ];
    }

    /**
     * Get type list
     */
    public function getTypeList()
    {
        $options = $this->toOptionArray();
        $typeList = [];
        foreach ($options as $option) {
            $typeList[] = $option['value'];
        }

        return $typeList;
    }
}
