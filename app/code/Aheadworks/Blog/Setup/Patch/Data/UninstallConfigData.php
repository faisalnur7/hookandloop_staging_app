<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Class UninstallConfigData
 */
class UninstallConfigData implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UninstallConfigData constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
    }

    /**
     * Uninstall module data from config
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $installer = $this->moduleDataSetup;
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_blog%'");
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
