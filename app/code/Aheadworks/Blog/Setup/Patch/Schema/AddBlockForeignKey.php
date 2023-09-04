<?php
namespace Aheadworks\Blog\Setup\Patch\Schema;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddBlockForeignKey
 */
class AddBlockForeignKey implements SchemaPatchInterface, PatchVersionInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param SchemaSetupInterface $schemaSetup
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup,
        MetadataPool $metadataPool
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->metadataPool = $metadataPool;
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
     * Apply schema patch
     *
     * @throws \Exception
     */
    public function apply()
    {
        $connection = $this->schemaSetup->getConnection();
        $connection->addForeignKey(
            $this->schemaSetup->getFkName(
                'aw_blog_category',
                'cms_block_id',
                'cms_block',
                'block_id'
            ),
            $this->schemaSetup->getTable('aw_blog_category'),
            'cms_block_id',
            $this->schemaSetup->getTable('cms_block'),
            $this->metadataPool->getMetadata(BlockInterface::class)
                ->getLinkField(),
            Table::ACTION_SET_NULL
        );
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.1.1';
    }
}