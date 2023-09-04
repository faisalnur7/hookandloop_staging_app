<?php
namespace Aheadworks\Blog\Setup\Patch\Schema;

use Aheadworks\Blog\Model\ResourceModel\Post;
use Aheadworks\Blog\Model\Source\Post\AuthorDisplayMode;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Class UpdateAuthorDisplayMode
 */
class UpdateAuthorDisplayMode implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * UpdateAuthorDisplayMode constructor.
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
     * Set Default Value Author Display Mode
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->changeColumn(
            $this->moduleDataSetup->getTable(Post::BLOG_POST_TABLE),
            'author_display_mode',
            'author_display_mode',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => false,
                'default' => AuthorDisplayMode::USE_DEFAULT_OPTION,
                'comment' => 'Author Display Mode'
            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
