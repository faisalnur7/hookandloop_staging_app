<?php
namespace Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata;

use Aheadworks\Blog\Model\StoreResolver;

/**
 * Class UrlConfigMetadataGenerator
 * @package Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata
 */
class UrlConfigMetadataGenerator
{
    /**
     * @var StoreUrlConfigMetadataFactory
     */
    private $storeUrlConfigMetadataFactory;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * UrlConfigMetadataGenerator constructor.
     * @param StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        StoreUrlConfigMetadataFactory $storeUrlConfigMetadataFactory,
        StoreResolver $storeResolver
    ) {
        $this->storeUrlConfigMetadataFactory = $storeUrlConfigMetadataFactory;
        $this->storeResolver = $storeResolver;
    }

    /**
     * Generates UrlConfigMetadata objects for store, website stores or all stores if store|website not defined
     *
     * @param int|null $websiteId
     * @param int|null $storeId
     * @return UrlConfigMetadata[]
     */
    public function generate($websiteId = null, $storeId = null)
    {
        if ($storeId) {
            $result = [$this->storeUrlConfigMetadataFactory->create($storeId)];
        } elseif ($websiteId) {
            $result = $this->generateForWebsite($websiteId);
        } else {
            $result = $this->generateForAllStores();
        }

        return $result;
    }

    /**
     * @param int $websiteId
     * @return UrlConfigMetadata[]
     */
    private function generateForWebsite($websiteId)
    {
        $websiteStores = $this->storeResolver->getStoreIds([$websiteId]);

        $result = [];
        foreach ($websiteStores as $store) {
            $result[] = $this->storeUrlConfigMetadataFactory->create($store);
        }

        return $result;
    }

    /**
     * @return UrlConfigMetadata[]
     */
    private function generateForAllStores()
    {
        $stores = $this->storeResolver->getAllStoreIds();

        $result = [];
        foreach ($stores as $store) {
            $result[] = $this->storeUrlConfigMetadataFactory->create($store);
        }

        return $result;
    }
}
