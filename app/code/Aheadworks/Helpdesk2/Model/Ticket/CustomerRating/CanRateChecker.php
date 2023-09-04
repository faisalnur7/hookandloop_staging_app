<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\CustomerRating;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Info;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CanRateChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\CustomerRating
 */
class CanRateChecker
{
    /**
     * CanRateChecker constructor.
     *
     * @param DateTime $dateTime
     * @param Info $messageInfo
     * @param int $duringDays
     */
    public function __construct(
        private DateTime $dateTime,
        private Info $messageInfo,
        private $duringDays = 15
    ) {
    }

    /**
     * Check if customer can rate the ticket
     *
     * @param TicketInterface $ticket
     * @return bool
     */
    public function canRate($ticket)
    {
        $lastMessage = $this->messageInfo->getLastMessage((int)$ticket->getEntityId());
        $lastMessageTimeStamp = $this->dateTime->timestamp($lastMessage->getCreatedAt());
        $currentTimestamp = $this->dateTime->timestamp();

        return ($currentTimestamp - $lastMessageTimeStamp) <= $this->duringDays * 86400;
    }
}
