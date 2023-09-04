<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class Priority
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class Priority extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_priority';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TicketPriorityInterface::ID);
    }
}
