<?php
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AuthorizationType
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class AuthorizationType implements OptionSourceInterface
{
    /**
     * Authorization types
     */
    const GOOGLE_TYPE = 'google';
    const DEFAULT_TYPE = 'default';
    const MICROSOFT_TYPE = 'microsoft';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Default'),
                'value' => self::DEFAULT_TYPE
            ],
            [
                'label' => __('Google'),
                'value' => self::GOOGLE_TYPE
            ],
            [
                'label' => __('Microsoft'),
                'value' => self::MICROSOFT_TYPE
            ]
        ];
    }
}
