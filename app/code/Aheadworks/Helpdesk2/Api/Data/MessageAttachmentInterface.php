<?php
namespace Aheadworks\Helpdesk2\Api\Data;

/**
 * Interface MessageAttachmentInterface
 * @api
 */
interface MessageAttachmentInterface extends AttachmentInterface
{
    const MESSAGE_ID = 'message_id';

    /**
     * Get message ID
     *
     * @return int
     */
    public function getMessageId();

    /**
     * Set mesasge ID
     *
     * @param int $id
     * @return $this
     */
    public function setMessageId($id);
}
