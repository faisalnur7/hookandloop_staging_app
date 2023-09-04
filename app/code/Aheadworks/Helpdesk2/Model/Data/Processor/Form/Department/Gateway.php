<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department\Gateway as GatewayPostProcessor;
use Aheadworks\Helpdesk2\Model\Source\Gateway\NotAssignedList;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Department
 */
class Gateway implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (empty($data[DepartmentInterface::GATEWAY_IDS])) {
            $data[GatewayPostProcessor::GATEWAY_SELECT_FIELD] = NotAssignedList::NOT_ASSIGNED_VALUE;
        } else {
            $gatewayIds = $data[DepartmentInterface::GATEWAY_IDS];
            $data[GatewayPostProcessor::GATEWAY_SELECT_FIELD] = reset($gatewayIds);
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
