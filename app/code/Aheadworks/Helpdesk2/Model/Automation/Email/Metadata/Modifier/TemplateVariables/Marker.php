<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class Marker
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Marker implements ModifierInterface
{
    const HISTORY_MARKER_FLAG_NAME = 'aw_helpdesk2_history_marker_flag';

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $templateVariables[self::HISTORY_MARKER_FLAG_NAME] = true;
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
