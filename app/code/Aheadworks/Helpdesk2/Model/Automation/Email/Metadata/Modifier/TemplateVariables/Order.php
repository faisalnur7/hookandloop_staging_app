<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Order implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        if (!$eventData->getOrder()) {
            throw new LocalizedException(__('Order must be present in event data'));
        }
        $templateVariables = $emailMetadata->getTemplateVariables();
        $templateVariables[EmailVariables::ORDER] = $eventData->getOrder();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
