<?php
declare(strict_types=1);

namespace Aheadworks\Blog\Model\Source\Post;

use Magento\Framework\Data\OptionSourceInterface;

class ViewMode implements OptionSourceInterface
{
    const GRID = 'grid';
    const LIST = 'list';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::GRID,
                'label' => __('Grid')
            ],
            [
                'value' => self::LIST,
                'label' => __('List')
            ]
        ];
    }
}