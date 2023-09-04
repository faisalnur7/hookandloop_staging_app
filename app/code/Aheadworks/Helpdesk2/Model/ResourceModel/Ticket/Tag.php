<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Tag resource model
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Tag extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_tag';
    const RELATION_TABLE_NAME = 'aw_helpdesk2_ticket_tag';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, 'id');
    }
}
