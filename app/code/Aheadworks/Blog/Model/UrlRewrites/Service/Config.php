<?php
namespace Aheadworks\Blog\Model\UrlRewrites\Service;

use Aheadworks\Blog\Model\StoreResolver;
use Aheadworks\Blog\Model\UrlRewrites\RewriteUpdater;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\StoreUrlConfigMetadataFactory;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;
use Aheadworks\Blog\Model\UrlRewrites\Generator\AbstractGenerator as RewriteGenerator;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;

/**
 * Class Config
 * @package Aheadworks\Blog\Model\UrlRewrites\Service
 */
class Config
{
    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var RewriteUpdater
     */
    private $rewriteUpdater;

    /**
     * @var RewriteGenerator
     */
    private $rewriteGenerator;

    /**
     * @var MergeDataProviderFactory
     */
    private $mergeDataProviderFactory;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * Config constructor.
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param RewriteUpdater $rewriteUpdater
     * @param RewriteGenerator $rewriteGenerator
     * @param MergeDataProviderFactory $mergeDataProviderFactory
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        RewriteUpdater $rewriteUpdater,
        RewriteGenerator $rewriteGenerator,
        MergeDataProviderFactory $mergeDataProviderFactory,
        StoreResolver $storeResolver
    ) {
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->rewriteUpdater = $rewriteUpdater;
        $this->rewriteGenerator = $rewriteGenerator;
        $this->mergeDataProviderFactory = $mergeDataProviderFactory;
        $this->storeResolver = $storeResolver;
    }

    /**
     * Updates permanent redirects
     *
     * @param UrlConfigMetadataModel[]  $oldUrlConfigMetadata
     * @throws LocalizedException
     */
    public function updateRewrites($oldUrlConfigMetadata)
    {
        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($oldUrlConfigMetadata as $oldMetadataItem) {
            /** @var UrlConfigMetadataModel $newConfig */
            $newMetadataItem = $this->storeUrlConfigMetadataFactory->create($oldMetadataItem->getStoreId());

            $rewrites = $this->rewriteGenerator->generate(
                $newMetadataItem->getStoreId(),
                $newMetadataItem,
                $oldMetadataItem
            );

            $mergeDataProvider->merge($rewrites);
        }

        $this->rewriteUpdater->update($mergeDataProvider->getData());
    }

    /**
     * Generates all rewrites for module
     *
     * @param int[] $storeIds
     * @throws LocalizedException
     */
    public function generateAllRewrites($storeIds = [])
    {
        if (empty($storeIds)) {
            $storeIds = $this->storeResolver->getAllStoreIds();
        }

        /** @var MergeDataProvider $mergeDataProvider */
        $mergeDataProvider = $this->mergeDataProviderFactory->create();

        foreach ($storeIds as $store) {
            /** @var UrlConfigMetadataModel $newConfig */
            $metadataModel = $this->storeUrlConfigMetadataFactory->create($store);

            $rewrites = $this->rewriteGenerator->generate(
                $store,
                $metadataModel,
                null
            );

            $mergeDataProvider->merge($rewrites);
        }

        $this->rewriteUpdater->update($mergeDataProvider->getData());
    }
}
