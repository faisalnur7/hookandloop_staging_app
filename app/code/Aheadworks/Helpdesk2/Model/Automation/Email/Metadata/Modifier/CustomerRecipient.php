<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Email\MetadataInterface as EmailMetadataInterface;

class CustomerRecipient implements ModifierInterface
{
    /**
     * Add metadata to existing object using event data
     *
     * @param EmailMetadataInterface $emailMetadata
     * @param EventDataInterface $eventData
     * @return EmailMetadataInterface
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $entity = $eventData->getTicket() ?? $eventData->getOrder();
        $name = $entity instanceof TicketInterface ? $entity->getCustomerName() : $entity->getCustomerFirstname();
        $emailMetadata->setRecipientName($name);
        $emailMetadata->setRecipientEmail($entity->getCustomerEmail());
        return $emailMetadata;
    }
}
