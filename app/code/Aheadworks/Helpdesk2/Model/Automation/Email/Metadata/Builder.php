<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Email\MetadataInterface as EmailMetadataInterface;
use Aheadworks\Helpdesk2\Model\Email\MetadataInterfaceFactory as EmailMetadataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\ModifierPool;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata
 */
class Builder
{
    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var ModifierPool
     */
    private $emailMetadataModifierPool;

    /**
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param ModifierPool $emailMetadataModifierPool
     */
    public function __construct(
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        ModifierPool $emailMetadataModifierPool
    ) {
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->emailMetadataModifierPool = $emailMetadataModifierPool;
    }

    /**
     * Build email metadata for triggered event and action
     *
     * @param string $actionName
     * @param EventDataInterface $eventData
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    public function buildForAction($actionName, $eventData)
    {
        /** @var EmailMetadataInterface $emailMetadata */
        $emailMetadata = $this->emailMetadataFactory->create();
        $emailMetadataModifier = $this->emailMetadataModifierPool->getModifierForEvent(
            $eventData->getEventName() . '_' . $actionName
        );
        $emailMetadata = $emailMetadataModifier->addMetadata($emailMetadata, $eventData);

        return $emailMetadata;
    }
}
