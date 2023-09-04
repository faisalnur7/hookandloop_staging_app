<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Source\Config\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DefaultTab
 */
class DefaultTab implements OptionSourceInterface
{
    const ALL = 'all';
    const DISCUSSION = 'discussion';
    const NOTES = 'notes';
    const HISTORY = 'history';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL,
                'label' => __('All')
            ],
            [
                'value' => self::DISCUSSION,
                'label' => __('Discussion')
            ],
            [
                'value' => self::NOTES,
                'label' => __('Notes')
            ],
            [
                'value' => self::HISTORY,
                'label' => __('History')
            ]
        ];
    }
}
