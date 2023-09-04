<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Generator\Config;

use Aheadworks\Blog\Api\AuthorRepositoryInterface as AuthorRepository;
use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\UrlRewrite\EntityType as UrlRewriteEntityType;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\RoutePathBuilder;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\OptionProvider as UrlRewriteOptionProvider;
use Magento\UrlRewrite\Model\StorageInterface as RewriteStorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;

/**
 * Class AuthorListItemsRoutes
 * @package Aheadworks\Blog\Model\UrlRewrites\Generator\Config
 */
class AuthorListItemsRoutes extends AbstractGenerator
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
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var AuthorInterface[]
     */
    private $allAuthorsList;

    /**
     * AuthorListItemsRoutes constructor.
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param Config $config
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param PathBuilder $pathBuilder
     * @param RoutePathBuilder $routePathBuilder
     * @param RewriteStorageInterface $rewriteStorage
     * @param AuthorRepository $authorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $subordinateEntitiesGenerators
     */
    public function __construct(
        MergeDataProviderFactory $mergeDataProviderFactory,
        Config $config,
        UrlRewriteFactory $urlRewriteFactory,
        PathBuilder $pathBuilder,
        RoutePathBuilder $routePathBuilder,
        RewriteStorageInterface $rewriteStorage,
        AuthorRepository $authorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        $subordinateEntitiesGenerators = []
    ) {
        parent::__construct($mergeDataProviderFactory, $config, $subordinateEntitiesGenerators);

        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->pathBuilder = $pathBuilder;
        $this->routePathBuilder = $routePathBuilder;
        $this->rewriteStorage = $rewriteStorage;
        $this->authorRepository = $authorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        /** @var AuthorInterface $author */
        foreach ($this->getAllAuthorsList() as $author) {
            $requestPath = $this->pathBuilder->buildAuthorPath($oldEntityState, $author);
            $targetPath = $this->pathBuilder->buildAuthorPath($newEntityState, $author);

            $permanentRedirect = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR)
                ->setEntityId($author->getId())
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

        /** @var AuthorInterface $author */
        foreach ($this->getAllAuthorsList() as $author) {
            $requestPath = $this->pathBuilder->buildAuthorPath($newEntityState, $author);
            $targetPath = $this->routePathBuilder->buildAuthorPath($author);

            $controllerRewrite = $this->urlRewriteFactory->create()
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setEntityType(UrlRewriteEntityType::TYPE_AUTHOR)
                ->setEntityId($author->getId())
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

        foreach ($this->getAllAuthorsList() as $author) {
            $permanentRedirectRequestPath = $this->pathBuilder->buildAuthorPath($oldEntityState, $author);
            $permanentRedirectTargetPath = $this->pathBuilder->buildAuthorPath($newEntityState, $author);

            $existingPermanentRedirects = $this->rewriteStorage->findAllByData([
                UrlRewrite::REDIRECT_TYPE => UrlRewriteOptionProvider::PERMANENT,
                UrlRewrite::ENTITY_TYPE => UrlRewriteEntityType::TYPE_AUTHOR,
                UrlRewrite::ENTITY_ID => $author->getId(),
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
            || $oldEntityState->getRouteToAuthors() !== $newEntityState->getRouteToAuthors()
            || $oldEntityState->getAuthorUrlSuffix() !== $newEntityState->getAuthorUrlSuffix();
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
            || $oldEntityState->getAuthorUrlSuffix() !== $newEntityState->getAuthorUrlSuffix();
    }

    /**
     * Returns all authors list
     *
     * @return AuthorInterface[]
     */
    private function getAllAuthorsList()
    {
        if (empty($this->allAuthorsList)) {
            $this->allAuthorsList = $this->authorRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        }

        return $this->allAuthorsList;
    }
}
