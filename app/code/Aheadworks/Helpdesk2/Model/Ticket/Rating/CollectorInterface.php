<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Interface CollectorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating
 */
interface CollectorInterface
{
    /**
     * Collect rating
     *
     * @param TicketInterface $ticket
     * @return float
     */
    public function collect($ticket);
}
