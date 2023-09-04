<?php
namespace Aheadworks\Helpdesk2\Api\Data;

/**
 * Ticket Tag interface
 *
 * @api
 */
interface TicketTagInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TAG_ID = 'tag_id';
    const TICKET_ID = 'ticket_id';
    /**#@-*/

    /**
     * Get tag id
     *
     * @return int|null
     */
    public function getTagId();

    /**
     * Set tag id
     *
     * @param int $tagId
     * @return $this
     */
    public function setTagId($tagId);

    /**
     * Get ticket id
     *
     * @return int|null
     */
    public function getTicketId();

    /**
     * Set ticket id
     *
     * @param int $ticketId
     * @return $this
     */
    public function setTicketId($ticketId);
}
