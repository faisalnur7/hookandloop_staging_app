<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Store;

use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractHandler;

class ReadHandler extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME);
    }

    /**
     * Process object
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($entity, $arguments)
    {
        $storeIds = $this->getStoreRelationData($entity->getId());
        $entity->setStoreIds($storeIds);

        return $entity;
    }

    /**
     * Retrieve store relation data
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    private function getStoreRelationData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, 'store_id')
            ->where('department_id = :id');

        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
