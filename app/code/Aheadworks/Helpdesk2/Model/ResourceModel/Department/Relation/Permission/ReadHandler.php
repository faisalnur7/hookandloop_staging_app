<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Permission;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractHandler;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Model\Department\Permission\Converter as PermissionConverter;

class ReadHandler extends AbstractHandler
{
    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param PermissionConverter $permissionConverter
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        private readonly PermissionConverter $permissionConverter
    ) {
        parent::__construct($metadataPool, $resourceConnection);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME);
    }

    /**
     * Process object
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($entity, $arguments)
    {
        $permissions = $this->getPermissionRelationData($entity->getId());
        $entity->setPermissions($permissions);

        return $entity;
    }

    /**
     * Retrieve permission relation data
     *
     * @param int $entityId
     * @return DepartmentPermissionInterface
     * @throws \Exception
     */
    private function getPermissionRelationData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, ['role_id', 'type'])
            ->where('department_id = :id');
        $permissionArray = $connection->fetchAll($select, ['id' => $entityId]);

        return $this->permissionConverter->fromDbArrayToDataObject($permissionArray);
    }
}
