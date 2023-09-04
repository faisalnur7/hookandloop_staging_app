<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Ticket
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Ticket implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $templateVariables[EmailVariables::TICKET] = $eventData->getTicket();
        $templateVariables[EmailVariables::TICKET_ID] = $eventData->getTicket()
            ? $eventData->getTicket()->getEntityId()
            : null;
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
