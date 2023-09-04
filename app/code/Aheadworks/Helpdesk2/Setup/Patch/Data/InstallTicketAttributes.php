<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Aheadworks\Helpdesk2\Setup\TicketEavSetup;
use Aheadworks\Helpdesk2\Setup\TicketEavSetupFactory;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;

class InstallTicketAttributes implements DataPatchInterface, PatchRevertableInterface, PatchVersionInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param TicketEavSetupFactory $ticketSetupFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly TicketEavSetupFactory $ticketSetupFactory
    ) {
    }

    /**
     * Install initial ticket attributes
     */
    public function apply()
    {
        /** @var TicketEavSetup $ticketEavSetup */
        $ticketEavSetup = $this->ticketSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $ticketEavSetup->installEntities();

        return $this;
    }

    /**
     * Remove all ticket attributes
     */
    public function revert()
    {
        $installer = $this->moduleDataSetup;
        $select = $installer->getConnection()->select()
            ->from($installer->getTable('eav_entity_type'), 'entity_type_id')
            ->where('entity_type_code = ?', TicketModel::ENTITY);
        $entityTypeId = $installer->getConnection()->fetchCol($select);

        $condition = ['entity_type_id = ?' => $entityTypeId];
        $installer->getConnection()->delete($installer->getTable('eav_entity_type'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_attribute'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_entity_attribute'), $condition);
        $installer->getConnection()->delete($installer->getTable('eav_attribute_set'), $condition);
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
