<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task;

use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Automation\Task as AutomationTaskModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Automation\Task\Status as TaskStatus;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = TaskInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(AutomationTaskModel::class, TaskResourceModel::class);
    }

    /**
     * Add ticket filter
     *
     * @param int $ticketId
     * @return $this
     */
    public function addTicketFilter($ticketId)
    {
        $this->addFieldToFilter(TaskInterface::TICKET_ID, $ticketId);
        return $this;
    }

    /**
     * Add action type filter
     *
     * @param string $actionType
     * @return $this
     */
    public function addActionTypeFilter($actionType)
    {
        $this->addFieldToFilter(TaskInterface::ACTION_TYPE, $actionType);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param string $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->addFieldToFilter(TaskInterface::STATUS, $status);
        return $this;
    }

    /**
     * Add undone status filter
     *
     * @return $this
     */
    public function addStatusUndoneFilter()
    {
        $this->addFieldToFilter(
            TaskInterface::STATUS,
            [
                'neq' => TaskStatus::DONE
            ]
        );

        return $this;
    }
}
