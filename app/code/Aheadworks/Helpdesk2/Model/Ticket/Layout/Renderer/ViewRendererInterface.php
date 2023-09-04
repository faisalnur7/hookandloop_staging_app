<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\RendererInterface;

/**
 * Interface ViewRendererInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer
 */
interface ViewRendererInterface extends RendererInterface
{
    const TICKET = 'ticket';

    /**
     * Get ticket
     *
     * @return TicketInterface
     */
    public function getTicket();

    /**
     * Set ticket
     *
     * @param TicketInterface $ticket
     * @return $this
     */
    public function setTicket($ticket);
}
