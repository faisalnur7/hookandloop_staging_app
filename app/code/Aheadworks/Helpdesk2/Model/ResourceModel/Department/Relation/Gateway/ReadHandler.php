<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Gateway;

use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractHandler;

class ReadHandler extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME);
    }

    /**
     * Process object
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($entity, $arguments)
    {
        $gatewayIds = $this->getGatewayRelationData($entity->getId());
        $entity->setGatewayIds($gatewayIds);

        return $entity;
    }

    /**
     * Retrieve gateway relation data
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    private function getGatewayRelationData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, 'gateway_id')
            ->where('department_id = :id');

        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
