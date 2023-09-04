<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\Source\Post\CustomerGroups;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class UpdateCustomerGroupsForPosts
 */
class UpdateCustomerGroupsForPosts implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateCustomerGroupsForPosts constructor.
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
     * Fill up all 'customer_groups' fields with 'all groups' value
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $connection->update(
            $this->moduleDataSetup->getTable(ResourcePost::BLOG_POST_TABLE),
            [
                'customer_groups' => CustomerGroups::ALL_GROUPS
            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.4.0';
    }
}
