<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config\Posts;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Listing\Builder as PostListBuilder;
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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PostsWithoutCategory
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Config\Posts
 */
class PostsWithoutCategory extends AbstractGenerator
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
     * array
     */
    private $postList;

    /**
     * array
     */
    private $postCategories;

    /**
     * PostsWithoutCategory constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param RewriteStorageInterface $rewriteStorage
     * @param PostListBuilder $postListBuilder
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
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->postListBuilder = $postListBuilder;
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
            $requestPath = $this->pathBuilder->buildPostPath($oldEntityState, $post);
            $targetPath = $this->pathBuilder->buildPostPath($newEntityState, $post);

            $permanentRedirect = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($post->getId())
                ->setStoreId($storeId)
                ->setRedirectType(UrlRewriteOptionProvider::PERMANENT);
            $mergeDataProvider->merge([$permanentRedirect]);
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
            $rewriteWithoutCategoryRequestPath = $this->pathBuilder->buildPostPath($newEntityState, $post);
            $targetPath = $this->routePathBuilder->buildPostPath($post);

            $controllerRewrite = $this->urlRewriteFactory->create()
                ->setRequestPath($rewriteWithoutCategoryRequestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_POST)
                ->setEntityId($post->getId())
                ->setStoreId($storeId);
            $mergeDataProvider->merge([$controllerRewrite]);
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

            $permanentRedirectRequestPath = $this->pathBuilder->buildPostPath($oldEntityState, $post);
            $permanentRedirectTargetPath = $this->pathBuilder->buildPostPath($newEntityState, $post);
            foreach ($existingPermanentRedirects as $existingRedirect) {
                $isOutdatedPermanentRedirect = $existingRedirect->getTargetPath() == $permanentRedirectRequestPath;

                if ($this->config->getSaveRewritesHistory($storeId) && $isOutdatedPermanentRedirect) {
                    $existingRedirect->setTargetPath($permanentRedirectTargetPath);
                    $mergeDataProvider->merge([$existingRedirect]);
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
        return $oldEntityState->getRouteToBlog() !== $newEntityState->getRouteToBlog()
            || $oldEntityState->getPostUrlSuffix() !== $newEntityState->getPostUrlSuffix();
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
            || $oldEntityState->getPostUrlSuffix() !== $newEntityState->getPostUrlSuffix()
            || $oldEntityState->getSeoUrlType() !== $newEntityState->getSeoUrlType();
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
}
