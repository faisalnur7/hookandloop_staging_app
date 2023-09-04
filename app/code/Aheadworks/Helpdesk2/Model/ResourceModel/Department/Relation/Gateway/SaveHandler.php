<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Gateway;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractSaveHandler;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Gateway
 */
class SaveHandler extends AbstractSaveHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process($entity, $arguments = [])
    {
        $this->deleteOldChildEntityById($entity->getId());
        $toInsert = $this->prepareGatewayRelationData($entity);
        $this->insertChildEntity($toInsert);

        return $entity;
    }

    /**
     * Retrieve array of gateway relation data to insert
     *
     * @param DepartmentInterface $entity
     * @return array
     */
    private function prepareGatewayRelationData($entity)
    {
        $relationData = [];
        foreach ($entity->getGatewayIds() as $gatewayId) {
            $relationData[] = [
                'department_id' => $entity->getId(),
                'gateway_id' => $gatewayId
            ];
        }

        return $relationData;
    }
}
