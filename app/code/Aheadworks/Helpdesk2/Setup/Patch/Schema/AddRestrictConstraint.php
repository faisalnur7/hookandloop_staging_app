<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Setup\Patch\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;

class AddRestrictConstraint implements SchemaPatchInterface, PatchRevertableInterface, PatchVersionInterface
{
    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        private readonly SchemaSetupInterface $schemaSetup
    ) {
    }

    /**
     * Add restrict constraint manually because of Magento issue
     * https://github.com/magento/magento2/issues/27072
     */
    public function apply()
    {
        $connection = $this->schemaSetup->getConnection();
        $connection->addForeignKey(
            $this->schemaSetup->getFkName(
                TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                TicketInterface::STATUS_ID,
                StatusResourceModel::MAIN_TABLE_NAME,
                TicketStatusInterface::ID
            ),
            $this->schemaSetup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::STATUS_ID,
            $this->schemaSetup->getTable(StatusResourceModel::MAIN_TABLE_NAME),
            TicketStatusInterface::ID,
            Table::ACTION_RESTRICT
        );
        $connection->addForeignKey(
            $this->schemaSetup->getFkName(
                TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                TicketInterface::PRIORITY_ID,
                PriorityResourceModel::MAIN_TABLE_NAME,
                TicketPriorityInterface::ID
            ),
            $this->schemaSetup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::PRIORITY_ID,
            $this->schemaSetup->getTable(PriorityResourceModel::MAIN_TABLE_NAME),
            TicketPriorityInterface::ID,
            Table::ACTION_RESTRICT
        );
        $connection->addForeignKey(
            $this->schemaSetup->getFkName(
                TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                TicketInterface::DEPARTMENT_ID,
                DepartmentResourceModel::MAIN_TABLE_NAME,
                DepartmentInterface::ID
            ),
            $this->schemaSetup->getTable(TicketResourceModel::TICKET_ENTITY_TABLE_NAME),
            TicketInterface::DEPARTMENT_ID,
            $this->schemaSetup->getTable(DepartmentResourceModel::MAIN_TABLE_NAME),
            DepartmentInterface::ID,
            Table::ACTION_RESTRICT
        );

        return $this;
    }

    /**
     * Remove patch on uninstall command in order to be able to install it again
     */
    public function revert()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '2.0.0';
    }
}
