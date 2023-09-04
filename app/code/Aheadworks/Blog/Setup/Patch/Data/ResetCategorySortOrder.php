<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class ResetCategorySortOrder
 */
class ResetCategorySortOrder implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * ResetCategorySortOrder constructor.
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

        $connection->update(
            $this->moduleDataSetup->getTable(ResourceCategory::BLOG_CATEGORY_TABLE),
            [CategoryInterface::SORT_ORDER => 0]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.6.0';
    }
}
