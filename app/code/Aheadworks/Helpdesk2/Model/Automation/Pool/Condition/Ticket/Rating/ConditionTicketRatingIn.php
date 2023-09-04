<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Pool\Condition\Ticket\Rating;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\ValidateConditionInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

class ConditionTicketRatingIn implements ValidateConditionInterface
{
    /**
     * @param EventDataInterface $eventData
     * @param array $condition
     * @return bool
     */
    public function isValid(EventDataInterface $eventData, array $condition)
    {
        /** @var TicketInterface $order */
        $ticket = $eventData->getTicket();
        $result = false;
        if (in_array($ticket->getCustomerRating(), $condition['value'])) {
            $result = true;
        }
        return $result;
    }
}
