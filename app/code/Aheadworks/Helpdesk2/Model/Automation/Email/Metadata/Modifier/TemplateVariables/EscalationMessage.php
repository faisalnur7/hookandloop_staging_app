<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class EscalationMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class EscalationMessage implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        if (!$eventData->getEscalationMessage()) {
            throw new LocalizedException(__('Escalation message must be present in event data'));
        }
        $templateVariables = $emailMetadata->getTemplateVariables();
        $templateVariables[EmailVariables::ESCALATION_MESSAGE] = $eventData->getEscalationMessage();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
