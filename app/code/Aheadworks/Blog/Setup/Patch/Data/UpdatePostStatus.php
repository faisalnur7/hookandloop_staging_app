<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class UpdatePostStatus
 */
class UpdatePostStatus implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdatePostStatus constructor.
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
     * Update post status
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();

        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $select = $connection->select()
            ->from($this->moduleDataSetup->getTable(ResourcePost::BLOG_POST_TABLE), ['id'])
            ->where('publish_date > ?', $now);
        $postIds = $connection->fetchCol($select);

        if (count($postIds)) {
            $connection->update(
                $this->moduleDataSetup->getTable(ResourcePost::BLOG_POST_TABLE),
                ['status' => Status::SCHEDULED],
                'id IN(' . implode(',', array_values($postIds)) . ')'
            );
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.1.0';
    }
}
