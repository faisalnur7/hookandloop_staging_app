<?php
namespace Aheadworks\Helpdesk2\Model\Source\Config\Order;

use Magento\Sales\Ui\Component\Listing\Column\Status\Options as OriginalStatusSource;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Config\Order
 */
class Status extends OriginalStatusSource
{
    const ALL = 'all';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $allOption = [
            'value' => self::ALL,
            'label' => __('All Statuses')
        ];

        $optionArray = parent::toOptionArray();
        array_unshift($optionArray, $allOption);

        return $optionArray;
    }
}
