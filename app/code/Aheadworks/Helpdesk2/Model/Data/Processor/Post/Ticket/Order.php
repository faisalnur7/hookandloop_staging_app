<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class Order implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[TicketInterface::ORDER_ID]) && empty($data[TicketInterface::ORDER_ID])) {
            unset($data[TicketInterface::ORDER_ID]);
        }

        return $data;
    }
}
