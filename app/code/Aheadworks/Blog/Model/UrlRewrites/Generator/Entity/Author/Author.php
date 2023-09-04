<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Author;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

/**
 * Class Author
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Author
 */
class Author extends AbstractGenerator
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
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * Author constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param Config $config
     * @param RewriteStorageInterface $rewriteStorage
     * @param RoutePathBuilder $routePathBuilder
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        Config $config,
        RewriteStorageInterface $rewriteStorage,
        RoutePathBuilder $routePathBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
    }

    /**
     * @inheritdoc
     * @param AuthorInterface $newEntityState
     * @param AuthorInterface $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);
        $permanentRedirectRequestPath = $this->pathBuilder->buildAuthorPath($urlConfigMetadata, $oldEntityState);
        $permanentRedirectTargetPath = $this->pathBuilder->buildAuthorPath($urlConfigMetadata, $newEntityState);

        $permanentRedirect = $this->urlRewriteFactory->create()
            ->setRequestPath($permanentRedirectRequestPath)
            ->setTargetPath($permanentRedirectTargetPath)
            ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR)
            ->setEntityId($newEntityState->getId())
            ->setStoreId($storeId)
            ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);

        return [$permanentRedirect];
    }

    /**
     * @inheritdoc
     * @param AuthorInterface $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);
        $controllerRewriteRequestPath = $this->pathBuilder->buildAuthorPath($urlConfigMetadata, $newEntityState);
        $controllerRewriteTargetPath = $this->routePathBuilder->buildAuthorPath($newEntityState);

        $controllerRewrite = $this->urlRewriteFactory->create()
            ->setRequestPath($controllerRewriteRequestPath)
            ->setTargetPath($controllerRewriteTargetPath)
            ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR)
            ->setEntityId($newEntityState->getId())
            ->setStoreId($storeId);

        return [$controllerRewrite];
    }

    /**
     * @inheritdoc
     * @param AuthorInterface $newEntityState
     * @param AuthorInterface $oldEntityState
     */
    protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);
        $permanentRedirectRequestPath = $this->pathBuilder->buildAuthorPath($urlConfigMetadata, $oldEntityState);
        $permanentRedirectTargetPath = $this->pathBuilder->buildAuthorPath($urlConfigMetadata, $newEntityState);

        $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
            UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
            UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_AUTHOR,
            UrlRewrite::ENTITY_ID => $newEntityState->getId(),
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
     * @param AuthorInterface $newEntityState
     * @param AuthorInterface $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey();
    }

    /**
     * @inheritdoc
     * @param AuthorInterface $newEntityState
     * @param AuthorInterface|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        $isUrlKeyChanged = false;
        if ($oldEntityState !== null) {
            $isUrlKeyChanged = $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey();
        }

        return $oldEntityState == null || $isUrlKeyChanged;
    }
}
