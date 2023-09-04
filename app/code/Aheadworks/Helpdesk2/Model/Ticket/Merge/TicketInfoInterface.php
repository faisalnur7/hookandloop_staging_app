<?php

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

/**
 * Interface TicketInfoInterface
 */
interface TicketInfoInterface
{
    const LAST_COMMENT = 'last_comment';

    const CUSTOMER_HISTORY_TICKETS_DISPLAY_COUNT = 8;
    const ADMIN_HISTORY_TICKETS_DISPLAY_COUNT = 4;

    /**
     * Get Uid
     *
     * @return string|null
     */
    public function getUid(): ?string;

    /**
     * Set Uid
     *
     * @param string $id
     * @return TicketInfoInterface
     */
    public function setUid($id): TicketInfoInterface;

    /**
     * Get entity id
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set entity id
     *
     * @param int $id
     * @return TicketInfoInterface
     */
    public function setEntityId($id): TicketInfoInterface;

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created at
     *
     * @param string $date
     * @return TicketInfoInterface
     */
    public function setCreatedAt($date): TicketInfoInterface;

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName(): string;

    /**
     * Set customer name
     *
     * @param string $name
     * @return TicketInfoInterface
     */
    public function setCustomerName($name): TicketInfoInterface;

    /**
     * Get subject
     *
     * @return string|null
     */
    public function getSubject(): ?string;

    /**
     * Set subject
     *
     * @param string $subject
     * @return TicketInfoInterface
     */
    public function setSubject($subject): TicketInfoInterface;

    /**
     * Set last comment
     *
     * @param string $comment
     * @return TicketInfoInterface
     */
    public function setLastComment($comment): TicketInfoInterface;

    /**
     * Get last comment
     *
     * @return string|null
     */
    public function getLastComment(): ?string;
}
