<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Agent;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\AbstractSaveHandler;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Agent
 */
class SaveHandler extends AbstractSaveHandler
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->initTable(DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME);
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
        $toInsert = $this->prepareAgentRelationData($entity);
        $this->insertChildEntity($toInsert);

        return $entity;
    }

    /**
     * Retrieve array of agent relation data to insert
     *
     * @param DepartmentInterface $entity
     * @return array
     */
    private function prepareAgentRelationData($entity)
    {
        $relationData = [];
        foreach ($entity->getAgentIds() as $agentId) {
            $relationData[] = [
                'department_id' => $entity->getId(),
                'agent_id' => $agentId
            ];
        }

        return $relationData;
    }
}
