<?php
namespace Aheadworks\Blog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class StoreResolver
 * @package Aheadworks\Blog\Model
 */
class StoreResolver
{
    const ALL_STORE_VIEWS = '0';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreWebsiteRelationInterface
     */
    private $storeWebsiteRelation;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreWebsiteRelationInterface $storeWebsiteRelation
     * @param Logger $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreWebsiteRelationInterface $storeWebsiteRelation,
        Logger $logger
    ) {
        $this->storeManager = $storeManager;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
        $this->logger = $logger;
    }

    /**
     * Get store ids
     *
     * @param int[] $websiteIds
     * @return array
     */
    public function getStoreIds($websiteIds)
    {
        $storeIdsArrays = [];
        foreach ($websiteIds as $websiteId) {
            $storeIdsArrays[] = $this->storeWebsiteRelation->getStoreByWebsiteId($websiteId);
        }

        return array_unique(array_merge(...$storeIdsArrays));
    }

    /**
     * Get all store ids
     *
     * @return int[]
     */
    public function getAllStoreIds()
    {
        return array_keys($this->storeManager->getStores());
    }

    /**
     * Retrieve website id list by store id list
     *
     * @param array $storeIdList
     * @return array
     */
    public function getWebsiteIdListByStoreIdList($storeIdList)
    {
        $websiteIdList = [];
        foreach ($storeIdList as $storeId) {
            if ($storeId == self::ALL_STORE_VIEWS) {
                $websiteIdList = array_keys($this->storeManager->getWebsites());
                break;
            }

            try {
                $websiteIdList[] = $this->storeManager->getStore($storeId)->getWebsiteId();
            } catch (NoSuchEntityException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }

        return array_unique($websiteIdList);
    }
}
