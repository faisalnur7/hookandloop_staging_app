<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = GridInterface::ENTITY_ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(TicketModel::class, TicketGridResourceModel::class);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            Ticket::TICKET_ENTITY_TABLE_NAME,
            TicketInterface::ENTITY_ID,
            TicketInterface::ENTITY_ID,
            TicketInterface::CUSTOMER_RATING,
            TicketInterface::CUSTOMER_RATING
        );

        return parent::_afterLoad();
    }
}
