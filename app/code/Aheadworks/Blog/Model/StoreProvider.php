<?php
namespace Aheadworks\Blog\Model;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StoreProvider
 */
class StoreProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * StoreProvider constructor.
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Has Single Store
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return $this->storeManager->hasSingleStore();
    }

    /**
     * Get Current Store Id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore(true)->getId();
    }

    /**
     * Get Store Id By Code
     *
     * @param string $code
     * @return int|null
     */
    public function getStoreIdByCode($code) {
        $stores = $this->storeManager->getStores(true, true);
        $store = $stores[$code] ?? null;

        return $store ? $store->getId() : null;
    }

    /**
     * Retrieve store Ids for reindex
     *
     * @return array
     */
    public function getStoreIdsForReindex()
    {
        $storeIds = [];
        $stores = $this->storeManager->getStores(true);

        foreach ($stores as $id => $store) {
            if (!$this->config->areRelatedProductsDisabled($id)) {
                $storeIds[] = $id;
            }
        }

        return $storeIds;
    }
}