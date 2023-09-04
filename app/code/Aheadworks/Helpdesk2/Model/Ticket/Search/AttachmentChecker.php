<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Search;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResource;

/**
 * Class AttachmentChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Search
 */
class AttachmentChecker
{
    /**
     * @var MessageResource
     */
    private $messageResource;

    /**
     * @param MessageResource $messageResource
     */
    public function __construct(
        MessageResource $messageResource
    ) {
        $this->messageResource = $messageResource;
    }

    /**
     * Check if attachment belong to ticket
     *
     * @param int $attachmentId
     * @param int $ticketId
     * @return bool
     * @throws LocalizedException
     */
    public function isAttachmentBelongToTicket($attachmentId, $ticketId)
    {
        return $ticketId == $this->messageResource->getTicketIdByAttachmentId($attachmentId);
    }
}
