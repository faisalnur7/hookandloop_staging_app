<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Store;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractSaveHandler;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Store
 */
class SaveHandler extends AbstractSaveHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME);
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
        $toInsert = $this->prepareStoreRelationData($entity);
        $this->insertChildEntity($toInsert);

        return $entity;
    }

    /**
     * Retrieve array of store relation data to insert
     *
     * @param DepartmentInterface $entity
     * @return array
     */
    private function prepareStoreRelationData($entity)
    {
        $relationData = [];
        foreach ($entity->getStoreIds() as $storeId) {
            $relationData[] = [
                'department_id' => $entity->getId(),
                'store_id' => $storeId
            ];
        }

        return $relationData;
    }
}
