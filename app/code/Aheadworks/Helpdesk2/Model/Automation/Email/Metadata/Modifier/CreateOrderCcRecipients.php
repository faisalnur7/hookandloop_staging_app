<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class CreateOrderCcRecipients
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class CreateOrderCcRecipients implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $emailMetadata->setRecipientEmail($eventData->getOrder()->getCustomerEmail());
        return $emailMetadata;
    }
}
