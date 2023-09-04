<?php
namespace Aheadworks\Blog\Model\Source\Config\Featured;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 */
class Position implements OptionSourceInterface
{
    const TOP = 'top';
    const SIDEBAR = 'sidebar';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::TOP,
                'label' => 'Top'
            ],
            [
                'value' => self::SIDEBAR,
                'label' => 'Sidebar'
            ]
        ];
    }
}