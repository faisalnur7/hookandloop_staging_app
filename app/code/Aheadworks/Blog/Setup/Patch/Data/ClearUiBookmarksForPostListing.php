<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class ClearUiBookmarksForPostListing
 */
class ClearUiBookmarksForPostListing implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * ClearUiBookmarksForPostListing constructor.
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
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('ui_bookmark');
        $connection->delete($table, ['namespace = ?' => 'aw_blog_post_listing']);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.13.0';
    }
}
