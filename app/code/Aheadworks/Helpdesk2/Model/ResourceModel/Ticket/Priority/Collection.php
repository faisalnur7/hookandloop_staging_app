<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority;

use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Ticket\Priority as PriorityModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(PriorityModel::class, PriorityResourceModel::class);
    }
}
