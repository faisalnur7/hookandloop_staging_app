<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Store
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Store implements ModifierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $entity = $eventData->getTicket() ?? $eventData->getOrder();
        $templateVariables = $emailMetadata->getTemplateVariables();
        $storeId = $entity->getStoreId();
        $templateVariables[EmailVariables::STORE] = $this->storeManager->getStore($storeId);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
