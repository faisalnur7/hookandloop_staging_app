<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\CollectorComposite;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class Rating
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket
 */
class Rating implements ProcessorInterface
{
    /**
     * @var CollectorComposite
     */
    private $collectorComposite;

    /**
     * @param CollectorComposite $collectorComposite
     */
    public function __construct(CollectorComposite $collectorComposite)
    {
        $this->collectorComposite = $collectorComposite;
    }

    /**
     * Prepare model before save
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelBeforeSave($ticket)
    {
        $ticket->setRating(0);
        if (in_array($ticket->getStatusId(), [TicketStatus::NEW, TicketStatus::OPEN])) {
            $this->collectorComposite->collect($ticket);
        }

        return $ticket;
    }

    /**
     * Prepare model after load
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelAfterLoad($ticket)
    {
        return $ticket;
    }
}
