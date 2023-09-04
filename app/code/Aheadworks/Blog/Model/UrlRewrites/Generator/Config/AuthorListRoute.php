<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config;

use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

/**
 * Class AuthorListRoute
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Config
 */
class AuthorListRoute extends AbstractGenerator
{
    const AUTHOR_LIST_REWRITE_ENTITY_ID = 1;

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
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * AuthorListRoute constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param RewriteStorageInterface $rewriteStorage
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        RoutePathBuilder $routePathBuilder,
        RewriteStorageInterface $rewriteStorage,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        $requestPath = $this->pathBuilder->buildBlogAuthorsPath($oldEntityState);
        $targetPath = $this->pathBuilder->buildBlogAuthorsPath($newEntityState);

        $permanentRedirect = $this->urlRewriteFactory->create()
            ->setRequestPath($requestPath)
            ->setTargetPath($targetPath)
            ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR_LIST_PAGE)
            ->setEntityId(self::AUTHOR_LIST_REWRITE_ENTITY_ID)
            ->setStoreId($storeId)
            ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);

        return [$permanentRedirect];
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        $requestPath = $this->pathBuilder->buildBlogAuthorsPath($newEntityState);
        $targetPath = $this->routePathBuilder->buildBlogAuthorsPath();

        $controllerRewrite = $this->urlRewriteFactory->create()
            ->setRequestPath($requestPath)
            ->setTargetPath($targetPath)
            ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR_LIST_PAGE)
            ->setEntityId(self::AUTHOR_LIST_REWRITE_ENTITY_ID)
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
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        $permanentRedirectRequestPath = $this->pathBuilder->buildBlogAuthorsPath($oldEntityState);
        $permanentRedirectTargetPath = $this->pathBuilder->buildBlogAuthorsPath($newEntityState);

        $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
            UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
            UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_AUTHOR_LIST_PAGE,
            UrlRewrite::ENTITY_ID => self::AUTHOR_LIST_REWRITE_ENTITY_ID,
            UrlRewrite::STORE_ID => $storeId
        ]);

        /** @var UrlRewrite $existingRewrite */
        foreach ($existingPermanentRedirects as $existingRedirect) {
            $isOutdatedPermanentRedirect = $existingRedirect->getTargetPath() == $permanentRedirectRequestPath;

            if ($this->config->getSaveRewritesHistory($storeId) && $isOutdatedPermanentRedirect) {
                $existingRedirect->setTargetPath($permanentRedirectTargetPath);
                $mergeDataProvider->merge([$existingRedirect]);
            }
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return $oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog()
            || $oldEntityState->getRouteToAuthors() !== $newEntityState->getRouteToAuthors()
            || $oldEntityState->getUrlSuffixForOtherPages() !== $newEntityState->getUrlSuffixForOtherPages();
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        return $oldEntityState == null
            || $oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog()
            || $oldEntityState->getRouteToAuthors() !== $newEntityState->getRouteToAuthors()
            || $oldEntityState->getUrlSuffixForOtherPages() !== $newEntityState->getUrlSuffixForOtherPages();
    }
}
