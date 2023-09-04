<?php
namespace Aheadworks\Helpdesk2\Model\Source\RejectingPattern;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Scope
 *
 * @package Aheadworks\Helpdesk2\Model\Source\RejectingPattern
 */
class Scope implements OptionSourceInterface
{
    /**
     * Scope types
     */
    const HEADERS = 'headers';
    const SUBJECT = 'subject';
    const BODY = 'body';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Headers'),
                'value' => self::HEADERS
            ],
            [
                'label' => __('Subject'),
                'value' => self::SUBJECT
            ],
            [
                'label' => __('Body'),
                'value' => self::BODY
            ]
        ];
    }
}
