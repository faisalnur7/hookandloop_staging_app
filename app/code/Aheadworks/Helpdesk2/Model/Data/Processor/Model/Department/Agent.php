<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department
 */
class Agent implements ProcessorInterface
{
    /**
     * Prepare model before save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelBeforeSave($department)
    {
        if (in_array(AgentList::NOT_ASSIGNED_VALUE, $department[DepartmentInterface::AGENT_IDS])) {
            $department[DepartmentInterface::AGENT_IDS] = [];
        }

        return $department;
    }

    /**
     * Prepare model after save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelAfterLoad($department)
    {
        return $department;
    }
}
