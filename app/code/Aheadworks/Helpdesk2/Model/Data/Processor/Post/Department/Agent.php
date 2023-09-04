<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department
 */
class Agent implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (!$data[DepartmentInterface::AGENT_IDS]) {
            $data[DepartmentInterface::AGENT_IDS] = [];
        }
        if (!$data[DepartmentInterface::PRIMARY_AGENT_ID]) {
            $data[DepartmentInterface::PRIMARY_AGENT_ID] = null;
        }

        return $data;
    }
}
