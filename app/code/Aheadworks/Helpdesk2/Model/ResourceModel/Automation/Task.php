<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Automation;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Model\Automation\TaskInterface;

/**
 * Class Task
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Automation
 */
class Task extends AbstractDb
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_automation_task';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TaskInterface::ID);
    }
}
