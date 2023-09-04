<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status;

use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Ticket\Status as StatusModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(StatusModel::class, StatusResourceModel::class);
    }
}
