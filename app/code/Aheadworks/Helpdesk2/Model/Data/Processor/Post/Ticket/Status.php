<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class Status implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[TicketInterface::STATUS_ID])) {
            $value = $data[TicketInterface::STATUS_ID];
            if (defined($value)) {
                $data[TicketInterface::STATUS_ID] = constant($value);
            }
        }

        return $data;
    }
}
