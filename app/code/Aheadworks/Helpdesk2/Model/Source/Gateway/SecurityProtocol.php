<?php
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SecurityProtocol
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class SecurityProtocol implements OptionSourceInterface
{
    /**
     * Security protocol types
     */
    const NONE = 'none';
    const SSL = 'SSL';
    const TLS = 'TLS';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('None'),
                'value' => self::NONE
            ],
            [
                'label' => __('SSL'),
                'value' => self::SSL
            ],
            [
                'label' => __('TLS'),
                'value' => self::TLS
            ]
        ];
    }
}
