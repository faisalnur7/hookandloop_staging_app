<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Category\Listing\Builder as CategoryListBuilder;
use Aheadworks\Blog\Model\Config;
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

/**
 * Class PostWithCategory
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post
 */
class PostWithCategory extends AbstractGenerator
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
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @var CategoryListBuilder
     */
    private $categoryListBuilder;

    /**
     * PostWithCategory constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param Config $config
     * @param RewriteStorageInterface $rewriteStorage
     * @param StoreSetOperations $storeSetOperations
     * @param RoutePathBuilder $routePathBuilder
     * @param CategoryListBuilder $categoryListBuilder
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
        CategoryListBuilder $categoryListBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->storeSetOperations = $storeSetOperations;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->categoryListBuilder = $categoryListBuilder;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        $oldAndNewCategoriesIntersection = array_intersect($oldEntityState->getCategoryIds(), $newEntityState->getCategoryIds());

        $this->categoryListBuilder
            ->getSearchCriteriaBuilder()
            ->addFilter(CategoryInterface::ID, $oldAndNewCategoriesIntersection, 'in')
            ->addFilter(CategoryInterface::STORE_IDS, [$storeId], 'in');
        /** @var CategoryInterface[] $postCategories */
        $postCategories = $this->categoryListBuilder->getCategoryList();

        foreach ($postCategories as $category) {
            $permanentRedirectWithCategoryRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $oldEntityState, $category);
            $permanentRedirectWithCategoryTargetPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $newEntityState, $category);

            $permanentRedirectWithCategory = $this->urlRewriteFactory->create()
                ->setRequestPath($permanentRedirectWithCategoryRequestPath)
                ->setTargetPath($permanentRedirectWithCategoryTargetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($newEntityState->getId())
                ->setStoreId($storeId)
                ->setRedirectType(UrlRewriteOptionProvider::PERMANENT)
                ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $category->getId()]);

            $mergeDataProvider->merge([$permanentRedirectWithCategory]);
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        $this->categoryListBuilder
            ->getSearchCriteriaBuilder()
            ->addFilter(CategoryInterface::ID, $newEntityState->getCategoryIds(), 'in')
            ->addFilter(CategoryInterface::STORE_IDS, [$storeId], 'in');
        /** @var CategoryInterface[] $postCategories */
        $newPostCategories = $this->categoryListBuilder->getCategoryList();
        $controllerRewriteWithCategoryTargetPath = $this->routePathBuilder->buildPostPath($newEntityState);

        foreach ($newPostCategories as $category) {
            $controllerRewriteWithCategoryRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $newEntityState, $category);

            $controllerRewriteWithCategory = $this->urlRewriteFactory->create()
                ->setRequestPath($controllerRewriteWithCategoryRequestPath)
                ->setTargetPath($controllerRewriteWithCategoryTargetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($newEntityState->getId())
                ->setStoreId($storeId)
                ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $category->getId()]);

            $mergeDataProvider->merge([$controllerRewriteWithCategory]);
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function getExistingRewrites($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        /** @var UrlConfigMetadataModel $urlConfigMetadata */
        $urlConfigMetadata = $this->storeUrlConfigMetadataFactory->create($storeId);

        $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
            UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
            UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_POST,
            UrlRewrite::ENTITY_ID => $newEntityState->getId(),
            UrlRewrite::STORE_ID => $storeId
        ]);

        $oldAndNewCategoriesIntersection = array_intersect($oldEntityState->getCategoryIds(), $newEntityState->getCategoryIds());

        $this->categoryListBuilder
            ->getSearchCriteriaBuilder()
            ->addFilter(CategoryInterface::ID, $oldAndNewCategoriesIntersection, 'in')
            ->addFilter(CategoryInterface::STORE_IDS, [$storeId], 'in');
        /** @var CategoryInterface[] $postCategories */
        $postCategories = $this->categoryListBuilder->getCategoryList();

        foreach ($postCategories as $category) {
            $permanentRedirectWithCategoryRequestPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $oldEntityState, $category);
            $permanentRedirectWithCategoryTargetPath = $this->pathBuilder->buildPostWithCategoryPath($urlConfigMetadata, $newEntityState, $category);

            /** @var UrlRewrite $existingRewrite */
            foreach ($existingPermanentRedirects as $existingRedirect) {
                $isOutdatedPermanentRedirect = $existingRedirect->getTargetPath() == $permanentRedirectWithCategoryRequestPath;

                if ($this->config->getSaveRewritesHistory($storeId) && $isOutdatedPermanentRedirect) {
                    $existingRedirect->setTargetPath($permanentRedirectWithCategoryTargetPath);
                    $mergeDataProvider->merge([$existingRedirect]);
                }
            }
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        $oldAndNewStoresIntersection = $this->storeSetOperations->getIntersection(
            $newEntityState->getStoreIds(),
            $oldEntityState->getStoreIds()
        );
        $isStoreWasAndRemained = in_array($storeId, $oldAndNewStoresIntersection);
        $isUrlKeyChanged = $oldEntityState->getUrlKey() !== $newEntityState->getUrlKey();

        return $this->config->isPostUrlIncludesCategory($storeId) && $isStoreWasAndRemained && $isUrlKeyChanged;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        $isStoresChanged = false;
        $isUrlKeyChanged = false;
        $categoriesChanged = false;
        $newPost = $oldEntityState == null;

        if ($oldEntityState !== null) {
            if (!$this->storeSetOperations->isEqual($newEntityState->getStoreIds(), $oldEntityState->getStoreIds())) {
                $isStoresChanged = true;
            }
            if ($newEntityState->getUrlKey() !== $oldEntityState->getUrlKey()) {
                $isUrlKeyChanged = true;
            }

            $newCategories = $newEntityState->getCategoryIds();
            $oldCategories = $oldEntityState->getCategoryIds();
            $categoriesChanged = count($oldCategories) != count($newCategories)
                || array_diff($oldCategories, $newCategories) !== array_diff($newCategories, $oldCategories);
        }

        return $this->config->isPostUrlIncludesCategory($storeId)
            && ($newPost || $isStoresChanged || $isUrlKeyChanged || $categoriesChanged);
    }
}
