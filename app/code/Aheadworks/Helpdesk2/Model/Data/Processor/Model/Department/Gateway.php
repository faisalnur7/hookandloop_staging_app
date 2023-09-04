<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department
 */
class Gateway implements ProcessorInterface
{
    /**
     * Prepare gateways to be saved
     *
     * Currently we cannot set more that one gateway to department
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelBeforeSave($department)
    {
        $gatewayIds = $department[DepartmentInterface::GATEWAY_IDS];
        if (count($gatewayIds) > 1) {
            $department[DepartmentInterface::GATEWAY_IDS] = [reset($gatewayIds)];
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
