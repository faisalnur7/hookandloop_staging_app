<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Email\MetadataInterface as EmailMetadataInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface ModifierInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email
 */
interface ModifierInterface
{
    /**
     * Add metadata to existing object using event data
     *
     * @param EmailMetadataInterface $emailMetadata
     * @param EventDataInterface $eventData
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    public function addMetadata($emailMetadata, $eventData);
}
