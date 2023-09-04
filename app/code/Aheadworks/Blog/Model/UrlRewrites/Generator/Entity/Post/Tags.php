<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Aheadworks\Blog\Api\TagRepositoryInterface as TagRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class Tags
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Entity\Post
 */
class Tags extends AbstractGenerator
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
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RewriteStorageInterface
     */
    private $rewriteStorage;

    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var TagInterface[]
     */
    private $allTags;

    /**
     * Tags constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param TagRepository $tagRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RewriteStorageInterface $rewriteStorage
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        RoutePathBuilder $routePathBuilder,
        TagRepository $tagRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RewriteStorageInterface $rewriteStorage,
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->tagRepository = $tagRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->allTags = [];
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function getPermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
       return [];
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

        foreach ($this->getAllTags() as $tag) {
            $requestPath = $this->pathBuilder->buildTagPath($urlConfigMetadata, $tag);
            $targetPath = $this->routePathBuilder->buildTagPath($tag);

            $controllerRewrite = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_TAG)
                ->setEntityId($tag->getId())
                ->setStoreId($storeId);
            $mergeDataProvider->merge([$controllerRewrite]);
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
        $allTagIds = [];
        foreach ($this->getAllTags() as $tag) {
            $allTagIds[] = $tag->getId();
        }

        $existingRedirects = [];
        if ($allTagIds) {
            $existingRedirects = $this->rewriteStorage->findAllByData([
                UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_TAG,
                UrlRewrite::ENTITY_ID => $allTagIds,
                UrlRewrite::STORE_ID => $storeId
            ]);
        }

        return $existingRedirects;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function isNeedGeneratePermanentRedirects($storeId, $newEntityState, $oldEntityState)
    {
        return false;
    }

    /**
     * @inheritdoc
     * @param PostInterface $newEntityState
     * @param PostInterface $oldEntityState
     */
    protected function isNeedGenerateControllerRewrites($storeId, $newEntityState, $oldEntityState)
    {
       return true;
    }

    /**
     * Returns all tags
     *
     * @return TagInterface[]
     * @throws LocalizedException
     */
    private function getAllTags()
    {
        if (empty($this->allTags)) {
            $this->allTags = $this->tagRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
        }

        return $this->allTags;
    }
}
