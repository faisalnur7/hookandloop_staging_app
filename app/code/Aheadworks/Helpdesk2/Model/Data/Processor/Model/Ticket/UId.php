<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Generator\UId as UIdGenerator;

/**
 * Class UId
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket
 */
class UId implements ProcessorInterface
{
    /**
     * @var UIdGenerator
     */
    private $uIdGenerator;

    /**
     * @param UIdGenerator $uIdGenerator
     */
    public function __construct(UIdGenerator $uIdGenerator)
    {
        $this->uIdGenerator = $uIdGenerator;
    }

    /**
     * Prepare model before save
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelBeforeSave($ticket)
    {
        if (!$ticket->getUid()) {
            $uid = $this->uIdGenerator->getUid();
            $ticket->setUid($uid);
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
