<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class UpdateCategoryPath
 */
class UpdateCategoryPath implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateCategoryPath constructor.
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
     * Update category path
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()
            ->from([$this->moduleDataSetup->getTable(ResourceCategory::BLOG_CATEGORY_TABLE)]);

        $categories = $connection->fetchAll($select);
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $categoryPath = $this->resolvePath($categories, $categoryId, true);
            $connection->update(
                [$this->moduleDataSetup->getTable(ResourceCategory::BLOG_CATEGORY_TABLE)],
                [CategoryInterface::PATH => $categoryPath],
                [CategoryInterface::ID . ' = ?' => $categoryId]
            );
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get full path for category
     *
     * @param array $categories
     * @param int $categoryId
     * @param bool $isInitial
     * @return string
     */
    private function resolvePath($categories, $categoryId, $isInitial = false)
    {
        $path = '';
        foreach ($categories as $category) {
            if ($category['id'] != $categoryId) {
                continue;
            }
            $parentId = $category['parent_id'];
            if ($isInitial && $parentId == 0) {
                return $categoryId;
            }
            if ($parentId != 0) {
                $categoryPath = $this->resolvePath($categories, $parentId);
                if ($categoryPath) {
                    $path .= $categoryPath . '/';
                }
                $path .= $parentId . ($isInitial ? '/' : '');
                if ($isInitial) {
                    $path .= $categoryId;
                    break;
                }
            } else {
                break;
            }
        }

        return $path;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.6.4';
    }
}
