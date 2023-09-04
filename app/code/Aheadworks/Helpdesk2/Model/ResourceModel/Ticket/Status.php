<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class Status extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_status';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TicketStatusInterface::ID);
    }
}
