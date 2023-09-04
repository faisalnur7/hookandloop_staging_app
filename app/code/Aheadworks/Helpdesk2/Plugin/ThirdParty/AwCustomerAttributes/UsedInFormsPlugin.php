<?php
namespace Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes;

/**
 * Class UsedInFormsPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes
 */
class UsedInFormsPlugin
{
    const ADMIN_TICKET_VIEW = 'aw_helpdesk2_adminhtml_ticket';

    /**
     * Add help desk admin ticket option
     *
     * @param \Aheadworks\CustomerAttributes\Model\Source\Attribute\UsedInForms $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToOptionArray($subject, $result)
    {
        $result[] = [
            'label' => __('Admin Ticket View'),
            'value' => self::ADMIN_TICKET_VIEW
        ];

        return $result;
    }
}
