<?php
namespace Aheadworks\Blog\Model\Source\Post;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AuthorDisplayMode
 */
class AuthorDisplayMode implements OptionSourceInterface
{
    const USE_DEFAULT_OPTION = -1;
    const DISPLAY = 1;
    const DISPLAY_NONE = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DISPLAY,
                'label' => __('Yes')
            ],
            [
                'value' => self::DISPLAY_NONE,
                'label' => __('No')
            ]
        ];
    }
}