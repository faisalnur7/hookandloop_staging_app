<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config\Posts;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Category\Listing\Builder as CategoryListBuilder;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Listing\Builder as PostListBuilder;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\Source\UrlRewrite\Metadata;
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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PostsWithCategory
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Config\Posts
 */
class PostsWithCategory extends AbstractGenerator
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
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @var PostListBuilder
     */
    private $postListBuilder;

    /**
     * @var CategoryListBuilder
     */
    private $categoryListBuilder;

    /**
     * array
     */
    private $postList;

    /**
     * @var array
     */
    private $postCategories;

    /**
     * PostsWithCategory constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param RewriteStorageInterface $rewriteStorage
     * @param PostListBuilder $postListBuilder
     * @param CategoryListBuilder $categoryListBuilder
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        RoutePathBuilder $routePathBuilder,
        RewriteStorageInterface $rewriteStorage,
        PostListBuilder $postListBuilder,
        CategoryListBuilder $categoryListBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->postListBuilder = $postListBuilder;
        $this->categoryListBuilder = $categoryListBuilder;
        $this->postList = [];
        $this->postCategories = [];
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($this->getPostList($storeId) as $post) {
            foreach ($this->getPostCategories($post, $storeId) as $category) {
                $requestPathWithCategory = $this->pathBuilder->buildPostWithCategoryPath($oldEntityState, $post, $category);
                $targetPathWithCategory = $this->pathBuilder->buildPostWithCategoryPath($newEntityState, $post, $category);

                $permanentRedirectWithCategory = $this->urlRewriteFactory->create()
                    ->setRequestPath($requestPathWithCategory)
                    ->setTargetPath($targetPathWithCategory)
                    ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                    ->setEntityId($post->getId())
                    ->setStoreId($storeId)
                    ->setRedirectType(UrlRewriteOptionProvider::PERMANENT)
                    ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $category->getId()]);
                $mergeDataProvider->merge([$permanentRedirectWithCategory]);
            }
        }

        return $mergeDataProvider->getData();
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     */
    protected function getControllerRewrites($storeId, $newEntityState)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($this->getPostList($storeId) as $post) {
            $rewriteWithCategoryTargetPath = $this->routePathBuilder->buildPostPath($post);

            foreach ($this->getPostCategories($post, $storeId) as $category) {
                $rewriteWithCategoryRequestPath = $this->pathBuilder->buildPostWithCategoryPath($newEntityState, $post, $category);

                $controllerRewriteWithCategory = $this->urlRewriteFactory->create()
                    ->setRequestPath($rewriteWithCategoryRequestPath)
                    ->setTargetPath($rewriteWithCategoryTargetPath)
                    ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                    ->setEntityId($post->getId())
                    ->setStoreId($storeId)
                    ->setMetadata([Metadata::REWRITE_METADATA_POST_CATEGORY => $category->getId()]);
                $mergeDataProvider->merge([$controllerRewriteWithCategory]);
            }
        }

        return $mergeDataProvider->getData();
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

        foreach ($this->getPostList($storeId) as $post) {
            $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
                UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_POST,
                UrlRewrite::ENTITY_ID => $post->getId(),
                UrlRewrite::STORE_ID => $storeId
            ]);
            foreach ($this->getPostCategories($post, $storeId) as $category) {
                $permanentRedirectWithCategoryRequestPath = $this->pathBuilder->buildPostWithCategoryPath($oldEntityState, $post, $category);
                $permanentRedirectWithCategoryTargetPath = $this->pathBuilder->buildPostWithCategoryPath($newEntityState, $post, $category);

                foreach ($existingPermanentRedirects as $existingRedirect) {
                    $isOutdatedPermanentRedirect = $existingRedirect->getTargetPath() == $permanentRedirectWithCategoryRequestPath;

                    if ($this->config->getSaveRewritesHistory($storeId) && $isOutdatedPermanentRedirect) {
                        $existingRedirect->setTargetPath($permanentRedirectWithCategoryTargetPath);
                        $mergeDataProvider->merge([$existingRedirect]);
                    }
                }
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
        return $this->config->isPostUrlIncludesCategory($storeId)
            && ($oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog()
                || $oldEntityState->getPostUrlSuffix() !== $newEntityState->getPostUrlSuffix());
    }

    /**
     * @inheritdoc
     * @param UrlConfigMetadataModel $newEntityState
     * @param UrlConfigMetadataModel|null $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
        $entityRewritesResponsibleFieldChanged = false;

        if ($oldEntityState) {
            $entityRewritesResponsibleFieldChanged = $oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog()
                || $oldEntityState->getPostUrlSuffix() !== $newEntityState->getPostUrlSuffix()
                || $oldEntityState->getSeoUrlType() !== $newEntityState->getSeoUrlType();
        }

        return $oldEntityState == null
            || ($this->config->isPostUrlIncludesCategory($storeId) && $entityRewritesResponsibleFieldChanged);
    }

    /**
     * Returns post list for store
     *
     * @param int $storeId
     * @return PostInterface[]|mixed
     * @throws LocalizedException
     */
    private function getPostList($storeId)
    {
        if (empty($this->postList[$storeId])) {
            $this->postListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(PostInterface::STORE_IDS, [$storeId], 'in');

            $this->postList[$storeId] = $this->postListBuilder->getPostList();
        }

        return $this->postList[$storeId];
    }

    /**
     * Returns post categories
     *
     * @param PostInterface $post
     * @param int $storeId
     * @return CategoryInterface[]
     * @throws LocalizedException
     */
    private function getPostCategories($post, $storeId)
    {
        if (empty($this->postCategories[$post->getId()][$storeId])) {
            $this->categoryListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(CategoryInterface::ID, $post->getCategoryIds(), 'in')
                ->addFilter(CategoryInterface::STORE_IDS, [$storeId], 'in');

            $this->postCategories[$post->getId()][$storeId] = $this->categoryListBuilder->getCategoryList();
        }

       return $this->postCategories[$post->getId()][$storeId];
    }
}
