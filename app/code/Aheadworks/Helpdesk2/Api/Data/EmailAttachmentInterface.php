<?php
namespace Aheadworks\Helpdesk2\Api\Data;

/**
 * Interface EmailAttachmentInterface
 *
 * @package Aheadworks\Helpdesk2\Api\Data
 */
interface EmailAttachmentInterface extends AttachmentInterface
{
    const EMAIL_ID = 'email_id';

    /**
     * Get email ID
     *
     * @return int
     */
    public function getEmailId();

    /**
     * Set mail ID
     *
     * @param int $emailId
     * @return $this
     */
    public function setEmailId($emailId);
}
