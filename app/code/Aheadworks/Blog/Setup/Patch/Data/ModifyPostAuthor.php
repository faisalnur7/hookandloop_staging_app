<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Post\Author\Resolver;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class ModifyPostAuthor
 */
class ModifyPostAuthor implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var Resolver
     */
    private $authorResolver;

    /**
     * ModifyPostAuthor constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Resolver $authorResolver
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Resolver $authorResolver
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->authorResolver = $authorResolver;
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
     * Modify post author
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $select = $connection->select()->from($this->moduleDataSetup->getTable(ResourcePost::BLOG_POST_TABLE));
        $posts = $connection->fetchAll($select);

        foreach ($posts as $post) {
            $authorId = $this->authorResolver->resolveId($post, 'author_name');
            $connection->update(
                $this->moduleDataSetup->getTable(ResourcePost::BLOG_POST_TABLE),
                [PostInterface::AUTHOR_ID => $authorId],
                PostInterface::ID . ' = ' . $post[PostInterface::ID]
            );
        }
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
