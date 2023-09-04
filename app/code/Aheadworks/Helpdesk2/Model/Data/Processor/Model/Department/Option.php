<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department;

use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department;

/**
 * Class Option
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department
 */
class Option implements ProcessorInterface
{
    /**
     * Prepare model before save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelBeforeSave($department)
    {
        if (empty($department[DepartmentInterface::OPTIONS])) {
            $department[DepartmentInterface::OPTIONS] = [];
        }

        return $department;
    }

    /**
     * Prepare model after load
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelAfterLoad($department)
    {
        return $department;
    }
}
