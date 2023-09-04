<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department
 */
class Agent implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (!$data[DepartmentInterface::AGENT_IDS]) {
            $data[DepartmentInterface::AGENT_IDS] = [AgentList::NOT_ASSIGNED_VALUE];
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
