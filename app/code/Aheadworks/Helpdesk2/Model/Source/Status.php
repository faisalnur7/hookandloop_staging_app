<?php
namespace Aheadworks\Helpdesk2\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * Status values
     */
    const ENABLED = '1';
    const DISABLED = '0';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ENABLED,  'label' => __('Enabled')],
            ['value' => self::DISABLED,  'label' => __('Disabled')],
        ];
    }
}
