<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Category\Listing\Builder as CategoryListBuilder;
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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Categories
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Config
 */
class Categories extends AbstractGenerator
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
     * @var CategoryListBuilder
     */
    private $categoryListBuilder;

    /**
     * @var array
     */
    private $categoryList;

    /**
     * Categories constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param RewriteStorageInterface $rewriteStorage
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
        CategoryListBuilder $categoryListBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory,$config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->categoryListBuilder = $categoryListBuilder;
        $this->categoryList = [];
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

        foreach ($this->getCategoryList($storeId) as $category) {
            $requestPath = $this->pathBuilder->buildCategoryPath($oldEntityState, $category);
            $targetPath = $this->pathBuilder->buildCategoryPath($newEntityState, $category);

            $permanentRedirect = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_CATEGORY)
                ->setEntityId($category->getId())
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

        foreach ($this->getCategoryList($storeId) as $category) {
            $requestPath = $this->pathBuilder->buildCategoryPath($newEntityState, $category);
            $targetPath = $this->routePathBuilder->buildCategoryPath($category);

            $controllerRewrite = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_CATEGORY)
                ->setEntityId($category->getId())
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

        foreach ($this->getCategoryList($storeId) as $category) {
            $permanentRedirectRequestPath = $this->pathBuilder->buildCategoryPath($oldEntityState, $category);
            $permanentRedirectTargetPath = $this->pathBuilder->buildCategoryPath($newEntityState, $category);

            $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
                UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_CATEGORY,
                UrlRewrite::ENTITY_ID => $category->getId(),
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
            || $oldEntityState->getUrlSuffixForOtherPages() !== $newEntityState->getUrlSuffixForOtherPages();
    }

    /**
     * Returns category list for store
     *
     * @param $storeId
     * @return CategoryInterface[]
     * @throws LocalizedException
     */
    private function getCategoryList($storeId)
    {
        if (empty($this->categoryList[$storeId])) {
            $this->categoryListBuilder
                ->getSearchCriteriaBuilder()
                ->addFilter(CategoryInterface::STORE_IDS, [$storeId], 'in');

            $this->categoryList[$storeId] = $this->categoryListBuilder->getCategoryList();
        }

        return $this->categoryList[$storeId];
    }
}
