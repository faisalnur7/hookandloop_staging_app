<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Model\UrlRewrites\Service\Config as UrlRewriteConfigService;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class GenerateControllerRewrites
 */
class GenerateControllerRewrites implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var UrlRewriteConfigService
     */
    private $urlRewriteConfigService;

    /**
     * GenerateControllerRewrites constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param UrlRewriteConfigService $urlRewriteConfigService
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        UrlRewriteConfigService $urlRewriteConfigService
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->urlRewriteConfigService = $urlRewriteConfigService;
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
     * Generates blog controller rewrites(blog uses magento url rewrites to process urls from ver. 2.8.1)
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->urlRewriteConfigService->generateAllRewrites();
        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
