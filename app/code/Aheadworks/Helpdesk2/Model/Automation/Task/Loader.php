<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Task;

use Magento\Framework\DataObject;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task\Collection as TaskCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task\CollectionFactory as TaskCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Automation\Task\Status as TaskStatus;

/**
 * Class Loader
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Task
 */
class Loader
{
    /**
     * @var TaskCollectionFactory
     */
    private $taskCollectionFactory;

    /**
     * @param TaskCollectionFactory $taskCollectionFactory
     */
    public function __construct(
        TaskCollectionFactory $taskCollectionFactory
    ) {
        $this->taskCollectionFactory = $taskCollectionFactory;
    }

    /**
     * Check if task is already scheduled
     *
     * @param string $actionType
     * @param int $ticketId
     * @return bool
     */
    public function isTaskAlreadyScheduled($actionType, $ticketId)
    {
        /** @var TaskCollection $taskCollection */
        $taskCollection = $this->taskCollectionFactory->create();
        $taskCollection
            ->addTicketFilter($ticketId)
            ->addActionTypeFilter($actionType)
            ->addStatusUndoneFilter();

        return $taskCollection->getSize() !== 0;
    }

    /**
     * Get pending tasks
     *
     * @return TaskInterface[]|DataObject[]
     */
    public function getPendingTasks()
    {
        /** @var TaskCollection $taskCollection */
        $taskCollection = $this->taskCollectionFactory->create();
        $taskCollection->addStatusFilter(TaskStatus::PENDING);

        return $taskCollection->getItems();
    }

    /**
     * Get running tasks
     *
     * @return TaskInterface[]|DataObject[]
     */
    public function getRunningTasks()
    {
        /** @var TaskCollection $taskCollection */
        $taskCollection = $this->taskCollectionFactory->create();
        $taskCollection->addStatusFilter(TaskStatus::RUNNING);

        return $taskCollection->getItems();
    }
}
