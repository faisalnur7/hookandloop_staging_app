<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class CcRecipients
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class CcRecipients implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $ccRecipients = $eventData->getTicket()->getCcRecipients();
        if ($ccRecipients) {
            $resultCc = array_map('trim', explode(',', $ccRecipients));
            $emailMetadata->setCcRecipients($resultCc);
        }

        return $emailMetadata;
    }
}
