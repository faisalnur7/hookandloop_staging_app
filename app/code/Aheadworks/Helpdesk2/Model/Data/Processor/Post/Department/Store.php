<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Store
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department
 */
class Store implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (!$data[DepartmentInterface::STORE_IDS]) {
            $data[DepartmentInterface::STORE_IDS] = [];
        }

        return $data;
    }
}
