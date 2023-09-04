<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department
 */
class Gateway implements ProcessorInterface
{
    const GATEWAY_SELECT_FIELD = 'gateway_id';

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $gatewayIds = $data[self::GATEWAY_SELECT_FIELD]
            ? [$data[self::GATEWAY_SELECT_FIELD]]
            : [];
        $data[DepartmentInterface::GATEWAY_IDS] = $gatewayIds;

        return $data;
    }
}
