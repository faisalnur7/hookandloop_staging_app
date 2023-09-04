<?php
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class GatewayProtocol
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class GatewayProtocol implements OptionSourceInterface
{
    /**
     * Authorization types
     */
    const POP3 = 'pop3';
    const IMAP = 'imap';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('POP3'),
                'value' => self::POP3
            ],
            [
                'label' => __('IMAP'),
                'value' => self::IMAP
            ],
        ];
    }
}
