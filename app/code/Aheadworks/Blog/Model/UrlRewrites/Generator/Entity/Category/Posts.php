<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Category;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Listing\Builder as PostListBuilder;
use Aheadworks\Blog\Model\Source\Config\Seo\UrlType;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\Source\UrlRewrite\Metadata;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\StoreSetOperations;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Posts
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Category
 */
class Posts extends AbstractGenerator
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
     * @var StoreSetOperations
     */
    private $storeSetOperations;

    /**
     * @var PostListBuilder
     */
    private $postListBuilder;

    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @var array
     */
    private $postList;

    /**
     * Posts constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param Config $config
     * @param RewriteStorageInterface $rewriteStorage
     * @param StoreSetOperations $storeSetOperations
     * @param RoutePathBuilder $routePathBuilder
     * @param PostListBuilder $postListBuilder
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        Config $config,
        RewriteStorageInterface $rewriteStorage,
        StoreSetOperations $storeSetOperations,
        RoutePathBuilder $routePathBuilder,
        PostListBuilder $postListBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->storeSetOperations = $storeSetOperations;
        $this->routePathBuilder = $routePathBuilder;
        $this->postListBuilder = $postListBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->postList = [];
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     * @param CategoryInterface $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();
        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        foreach ($this->getPostList($storeId, $newEntityState) as $post) {
            $permanentRedirectRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $post, $oldEntityState);
            $permanentRedirectTargetPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $post, $newEntityState);

            $permanentRedirect = $this->urlRewriteFactory->create()
                ->setRequestPath($permanentRedirectRequestPath)
                ->setTargetPath($permanentRedirectTargetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($post->getId())
                ->setStoreId($storeId)
                ->setRedirectType(UrlRewriteOptionProvider::PERMANENT)
                ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $newEntityState->getId()]);

            $mergeDataProvider->merge([$permanentRedirect]);
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        foreach ($this->getPostList($storeId, $newEntityState) as $post) {
            $controllerRewriteRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $post, $newEntityState);
            $controllerRewriteTargetPath = $this->routePathBuilder->buildPostPath($post);

            $controllerRewrite = $this->urlRewriteFactory->create()
                ->setRequestPath($controllerRewriteRequestPath)
                ->setTargetPath($controllerRewriteTargetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($post->getId())
                ->setStoreId($storeId)
                ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $newEntityState->getId()]);

            $mergeDataProvider->merge([$controllerRewrite]);
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     * @param CategoryInterface $oldEntityState
     */
    protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();
        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        foreach ($this->getPostList($storeId, $newEntityState) as $post) {
            $permanentRedirectRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $post, $oldEntityState);
            $permanentRedirectTargetPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $post, $newEntityState);

            $existingRewrites = $this->rewriteStorage->findAllByData([
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_POST,
                UrlRewrite::ENTITY_ID => $post->getId(),
                UrlRewrite::STORE_ID => $storeId
            ]);

            /** @var UrlRewrite $existingRewrite */
            foreach ($existingRewrites as $existingRewrite) {
                $isControllerRewrite = empty($existingRewrite->getRedirectType());
                $isPermanentRedirect = $existingRewrite->getRedirectType() == UrlRewriteOptionProvider::PERMANENT;

                $isControllerRewriteForAnotherCategory = $isControllerRewrite
                    && !empty($existingRewrite->getMetadata()[Metadata::REWRITE_METADATA_POST_CATEGORY])
                    && $existingRewrite->getMetadata()[Metadata::REWRITE_METADATA_POST_CATEGORY] != $newEntityState->getId();

                $isOutdatedPermanentRedirect = $isPermanentRedirect
                    && $existingRewrite->getTargetPath() == $permanentRedirectRequestPath;

                $isRewriteWithoutCategory = empty($existingRewrite->getMetadata()[Metadata::REWRITE_METADATA_POST_CATEGORY]);

                if ($isOutdatedPermanentRedirect) {
                    $existingRewrite->setTargetPath($permanentRedirectTargetPath);
                }

                if (($isPermanentRedirect && $this->config->getSaveRewritesHistory($storeId))
                    || $isControllerRewriteForAnotherCategory
                    || $isRewriteWithoutCategory) {
                    $mergeDataProvider->merge([$existingRewrite]);
                }
            }
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     * @param CategoryInterface $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        $result = false;

        if ($oldEntityState !== null) {
            $oldAndNewStoresIntersection = $this->storeSetOperations->getIntersection(
                $newEntityState->getStoreIds(),
                $oldEntityState->getStoreIds()
            );
            $isStoreWasAndRemained = in_array($storeId, $oldAndNewStoresIntersection);
            $isUrlKeyChanged = $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey();

            $result = $this->config->isPostUrlIncludesCategory($storeId)
                && $isStoreWasAndRemained
                && $isUrlKeyChanged;
        }

        return $result;
    }

    /**
     * @inheritdoc
     * @param CategoryInterface $newEntityState
     * @param CategoryInterface|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        $isStoresChanged = false;
        $isUrlKeyChanged = false;

        if ($oldEntityState !== null) {
            if (!$this->storeSetOperations->isEqual($newEntityState->getStoreIds(), $oldEntityState->getStoreIds())) {
                $isStoresChanged = true;
            }
            if ($newEntityState->getUrlKey() !== $oldEntityState->getUrlKey()) {
                $isUrlKeyChanged = true;
            }
        }

        return $this->config->isPostUrlIncludesCategory($storeId)
            && ($oldEntityState == null || $isStoresChanged || $isUrlKeyChanged);
    }

    /**
     * @param int $storeId
     * @param CategoryInterface $newEntityState
     * @return PostInterface[]
     * @throws LocalizedException
     */
    private function getPostList($storeId, $newEntityState)
    {
        if (empty($this->postList[$storeId])) {
            $this->postListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(PostInterface::CATEGORY_IDS, [$newEntityState->getId()], 'in')
                ->addFilter(PostInterface::STORE_IDS, [$storeId], 'in');

            /** @var PostInterface[] $posts */
            $this->postList[$storeId] = $this->postListBuilder->getPostList();
        }

        return $this->postList[$storeId];
    }
}
