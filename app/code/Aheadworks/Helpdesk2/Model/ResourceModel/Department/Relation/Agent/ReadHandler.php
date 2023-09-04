<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Agent;

use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractHandler;

class ReadHandler extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME);
    }

    /**
     * Process object
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function process($entity, $arguments)
    {
        $agentIds = $this->getAgentRelationData($entity->getId());
        $entity->setAgentIds($agentIds);

        return $entity;
    }

    /**
     * Retrieve agent relation data
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    private function getAgentRelationData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName, 'agent_id')
            ->where('department_id = :id');

        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
