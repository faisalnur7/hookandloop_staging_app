<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config;

use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

/**
 * Class Search
 */
class Search extends AbstractGenerator
{
    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var PathBuilder
     */
    private $pathBuilder;

    /**
     * @var RoutePathBuilder
     */
    private $routePathBuilder;

    /**
     * Search constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        RoutePathBuilder $routePathBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return [];
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        $requestPath = $this->pathBuilder->buildSearchPath($newEntityState);
        $targetPath = $this->routePathBuilder->buildSearchPath();

        $controllerRewrite = $this->urlRewriteFactory->create()
            ->setRequestPath($requestPath)
            ->setTargetPath($targetPath)
            ->setEntityType(UrlRewriteEntityType::TYPE_SEARCH)
            ->setEntityId(1)
            ->setStoreId($storeId);

        return [$controllerRewrite];
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState)
    {
        return [];
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return false;
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        return $oldEntityState == null
            || $oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog();
    }
}
