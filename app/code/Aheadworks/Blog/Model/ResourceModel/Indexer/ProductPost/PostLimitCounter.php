<?php
namespace Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost;

use Aheadworks\Blog\Model\Config;

/**
 * Class PostLimitCounter
 */
class PostLimitCounter
{
    const START_INCREMENT_VALUE = 1;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $data = [];

    /**
     * PostLimitCounter constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if Fully Filled Posts
     *
     * @param int $postId
     * @param array $storeIds
     * @return bool
     */
    public function isFullyFilled($postId, $storeIds)
    {
        $result = true;

        foreach ($storeIds as $storeId) {
            $isFilledForTheStore = $this->isFilledForTheStore($postId, $storeId);
            if (!$isFilledForTheStore) {
                $result = false;

                break;
            }
        }

        return $result;
    }

    /**
     * Check if Filled Post For The Store
     *
     * @param int $postId
     * @param int $storeId
     * @return bool
     */
    public function isFilledForTheStore($postId, $storeId)
    {
        $result = false;
        $isNeedUseLimit = $this->config->isUseProductsLimitInPosts($storeId);

        if ($isNeedUseLimit) {
            $this->data[$postId][$storeId] = $this->data[$postId][$storeId] ?? 0;
            $result = $this->data[$postId][$storeId] >= (int)$this->config->getRelatedProductsLimit($storeId);
        }

        return $result;
    }

    /**
     * Add the counter value for the post in the store
     *
     * @param int $postId
     * @param int $storeId
     * @return $this
     */
    public function increment($postId, $storeId)
    {
        if (!$this->isFilledForTheStore($postId, $storeId)) {
            $this->data[$postId][$storeId] = isset($this->data[$postId][$storeId])
                ? $this->data[$postId][$storeId] + self::START_INCREMENT_VALUE
                : self::START_INCREMENT_VALUE;
        }

        return $this;
    }

    /**
     * Clear counter
     *
     * @return $this
     */
    public function clear()
    {
        $this->data = [];

        return $this;
    }
}