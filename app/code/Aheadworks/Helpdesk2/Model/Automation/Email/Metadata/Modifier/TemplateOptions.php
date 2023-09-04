<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Magento\Framework\App\Area;

/**
 * Class TemplateOptions
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class TemplateOptions implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $entity = $eventData->getTicket() ?? $eventData->getOrder();
        $emailMetadata->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $entity->getStoreId(),
            ]
        );

        return $emailMetadata;
    }
}
