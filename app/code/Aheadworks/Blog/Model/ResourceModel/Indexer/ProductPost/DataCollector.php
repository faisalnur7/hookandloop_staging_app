<?php
namespace Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost;

use Aheadworks\Blog\Model\StoreProvider;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\StoreResolver;

/**
 * Class DataCollector
 *
 * @package Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost
 */
class DataCollector
{
    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @var StoreProvider
     */
    private $storeProvider;

    /**
     * @var PostLimitCounter
     */
    private $postLimitCounter;

    /**
     * @param StoreResolver $storeResolver
     * @param StoreProvider $storeProvider
     * @param PostLimitCounter $postLimitCounter
     */
    public function __construct(
        StoreResolver $storeResolver,
        StoreProvider $storeProvider,
        PostLimitCounter $postLimitCounter
    ) {
        $this->storeResolver = $storeResolver;
        $this->storeProvider = $storeProvider;
        $this->postLimitCounter = $postLimitCounter;
    }

    /**
     * Prepare and return data for insert to index table
     *
     * @param PostInterface $post
     * @param array $productIdsFilter
     * @return array
     */
    public function prepareProductPostData(PostInterface $post, $productIdsFilter = [])
    {
        $data = [];
        $allowedStoreIds = $this->getAllowedStoreIds($post);
        $postId = $post->getId();

        if (!$this->postLimitCounter->isFullyFilled($postId, $allowedStoreIds)
            && $post->getProductRule()->getConditions()->getConditions()
        ) {
            $websiteIds = $this->storeResolver->getWebsiteIdListByStoreIdList($allowedStoreIds);
            $productIds = $post->getProductRule()->setWebsiteIds($websiteIds)->getProductIds($productIdsFilter);

            foreach ($productIds as $productId => $validationByWebsite) {
                foreach ($websiteIds as $websiteId) {
                    if (empty($validationByWebsite[$websiteId])) {
                        continue;
                    }

                    if ($storeIdList = $this->storeResolver->getStoreIds([$websiteId])) {
                        $storeIdList = array_intersect($storeIdList, $allowedStoreIds);
                        foreach ($storeIdList as $storeId) {
                            if (!$this->postLimitCounter->isFilledForTheStore($postId, $storeId)) {
                                $data[] = [
                                    'product_id' => $productId,
                                    'post_id' => $post->getId(),
                                    'store_id' => $storeId
                                ];
                            }
                            $this->postLimitCounter->increment($postId, $storeId);
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Retrieve Allowed Store Ids
     *
     * @param PostInterface $post
     * @return array
     */
    private function getAllowedStoreIds($post)
    {
        $storeIdsForReindex = $this->storeProvider->getStoreIdsForReindex();
        $postStoreIds = $post->getStoreIds();

        return $postStoreIds && reset($postStoreIds)
            ? array_intersect($post->getStoreIds(), $storeIdsForReindex)
            : $storeIdsForReindex;
    }
}
