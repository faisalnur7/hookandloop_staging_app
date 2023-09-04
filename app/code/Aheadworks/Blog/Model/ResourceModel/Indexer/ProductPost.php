<?php
namespace Aheadworks\Blog\Model\ResourceModel\Indexer;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Indexer\Cache\Processor as IndexerCacheProcessor;
use Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost\PostLimitCounter;
use Aheadworks\Blog\Model\StoreProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\Blog\Model\Post\Provider as PostProvider;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Indexer\Model\ResourceModel\AbstractResource;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\Blog\Model\Post;
use Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost\DataCollector;
use Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost\DataProcessorFactory;
use Aheadworks\Blog\Model\Indexer\MultiThread\PostDimension;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost\DataProcessor\DataProcessorInterface;
use Aheadworks\Blog\Model\Indexer\Batch\Generator as IndexerBatchGenerator;
use Aheadworks\Blog\Model\Indexer\Batch\Resolver as IndexerBatchResolver;
use Magento\CatalogRule\Model\Indexer\IndexerTableSwapperInterface as TableSwapper;
use Magento\Framework\App\ResourceConnection;

/**
 * Class ProductPost
 *
 * @package Aheadworks\Blog\Model\ResourceModel\Indexer
 */
class ProductPost extends AbstractResource implements IdentityInterface
{
    /**#@+
     * Constants defined for tables
     */
    const BLOG_PRODUCT_POST_TABLE = 'aw_blog_product_post';
    const BLOG_PRODUCT_POST_INDEX_TABLE = 'aw_blog_product_post_idx';
    const BLOG_PRODUCT_POST_TMP_TABLE = 'aw_blog_product_post_tmp';
    /**#@-*/

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var DataCollector
     */
    private $dataCollector;

    /**
     * @var DataProcessorInterface
     */
    private $dataProcessor;

    /**
     * @var array
     */
    private $entities = [];

    /**
     * @var IndexerBatchGenerator
     */
    private $indexerBatchGenerator;

    /**
     * @var IndexerBatchResolver
     */
    private $indexerBatchResolver;

    /**
     * @var PostProvider
     */
    private $postProvider;

    /**
     * @var IndexerCacheProcessor
     */
    private $indexerCacheProcessor;

    /**
     * @var TableSwapper
     */
    private $indexerTableSwapper;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreProvider
     */
    private $storeProvider;

    /**
     * @var PostLimitCounter
     */
    private $postLimitCounter;

    /**
     * @param Context $context
     * @param StrategyInterface $tableStrategy
     * @param EventManagerInterface $eventManager
     * @param DataCollector $dataCollector
     * @param DataProcessorFactory $dataProcessorFactory
     * @param IndexerBatchGenerator $indexerBatchGenerator
     * @param IndexerBatchResolver $indexerBatchResolver
     * @param PostProvider $postProvider
     * @param StoreProvider $storeProvider
     * @param IndexerCacheProcessor $indexerCacheProcessor
     * @param TableSwapper $indexerTableSwapper
     * @param ResourceConnection $resource
     * @param PostLimitCounter $postLimitCounter
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StrategyInterface $tableStrategy,
        EventManagerInterface $eventManager,
        DataCollector $dataCollector,
        DataProcessorFactory $dataProcessorFactory,
        IndexerBatchGenerator $indexerBatchGenerator,
        IndexerBatchResolver $indexerBatchResolver,
        PostProvider $postProvider,
        StoreProvider $storeProvider,
        IndexerCacheProcessor $indexerCacheProcessor,
        TableSwapper $indexerTableSwapper,
        ResourceConnection $resource,
        PostLimitCounter $postLimitCounter,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->eventManager = $eventManager;
        $this->dataCollector = $dataCollector;
        $this->dataProcessor = $dataProcessorFactory->create();
        $this->indexerBatchGenerator = $indexerBatchGenerator;
        $this->indexerBatchResolver = $indexerBatchResolver;
        $this->postProvider = $postProvider;
        $this->indexerCacheProcessor = $indexerCacheProcessor;
        $this->indexerTableSwapper = $indexerTableSwapper;
        $this->resource = $resource;
        $this->storeProvider = $storeProvider;
        $this->postLimitCounter = $postLimitCounter;
    }

    /**
     * Define main product post index table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::BLOG_PRODUCT_POST_TABLE, 'product_id');
    }

    /**
     * Reindex all product post data
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $indexTable = $this->getWorkingTableName();
        $this->beginTransaction();
        try {
            $this->reindexData($indexTable);
            $this->indexerCacheProcessor->processFullReindex();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->swapIndexTable();
        return $this;
    }

    /**
     * Reindex product post data for defined ids
     *
     * @param array|int $ids
     * @return $this
     * @throws LocalizedException
     */
    public function reindexRows($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $this->beginTransaction();
        try {
            $connection = $this->getConnection();

            $connection->delete(
                $this->getMainTable(),
                ['post_id IN (?)' => $ids]
            );
            $storeIds = $this->storeProvider->getStoreIdsForReindex();
            $postListForReindex = $this->getPostsForReindex($ids, $storeIds);

            $this->insertProductPostListDataToTable($postListForReindex, $this->getMainTable());
            $this->postLimitCounter->clear();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Retrieve post list for reindex
     *
     * @param array $ids
     * @param array $storeIds
     * @return PostInterface[]
     * @throws LocalizedException
     */
    private function getPostsForReindex($ids, $storeIds)
    {
        return $this->postProvider
            ->getListForReindex($ids, $storeIds)
            ->getItems();
    }

    /**
     * Reindex product post data for dimension
     *
     * @param PostDimension $dimension
     * @param string $tableName
     * @return $this
     * @throws \Exception
     */
    public function reindexDimension($dimension, $tableName)
    {
        $this->beginTransaction();
        try {
            $postIds = $dimension->getValue();
            $this->reindexData($tableName, $postIds);

            $this->indexerCacheProcessor->processFullReindex();
            $this->commit();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Reindex Product Post Data
     *
     * @param array $postIds
     * @param string $tableName
     * @throws LocalizedException
     */
    private function reindexData($tableName, $postIds = [])
    {
        $connection = $this->getConnection();

        $batchRowCount = $this->indexerBatchResolver->getBatchRowCount(
            $connection
        );

        $batchIterator = $this->indexerBatchGenerator->getBatchIterator(
            $connection,
            ProductInterface::class,
            $batchRowCount
        );

        $storeIds = $this->storeProvider->getStoreIdsForReindex();

        if ($storeIds && $postListForReindex = $this->getPostsForReindex($postIds, $storeIds)) {
            foreach ($batchIterator as $batchQuery) {
                $productIdList = $connection->fetchCol($batchQuery);
                if (!empty($productIdList)) {
                    $this->insertProductPostListDataToTable($postListForReindex, $tableName, $productIdList);
                }
            }
        }

        $this->postLimitCounter->clear();
    }

    /**
     * @param PostInterface[] $postListForReindex
     * @param string $tableName
     * @param array $productIdList
     * @throws LocalizedException
     */
    private function insertProductPostListDataToTable($postListForReindex, $tableName, $productIdList = [])
    {
        foreach ($postListForReindex as $post) {
            $productPostData = $this->dataCollector->prepareProductPostData($post, $productIdList);
            if ($productPostData) {
                $this->dataProcessor->insertDataToTable($productPostData, $tableName);

                if (empty($productIdList)) {
                    $this->dispatchCleanCacheByTags($productPostData);
                }
            }
        }
    }

    /**
     * Dispatch clean_cache_by_tags event
     *
     * @param array $entities
     * @return void
     */
    private function dispatchCleanCacheByTags($entities = [])
    {
        $this->entities = $entities;
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * Retrieve working table name
     *
     * @return string
     */
    public function getWorkingTableName()
    {
        return $this->resource->getTableName(
            $this->indexerTableSwapper->getWorkingTableName(self::BLOG_PRODUCT_POST_TABLE)
        );
    }


    /**
     * Swap index table
     */
    public function swapIndexTable()
    {
        $this->indexerTableSwapper->swapIndexTables([self::BLOG_PRODUCT_POST_TABLE]);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->entities as $entity) {
            $postTag = Post::CACHE_TAG . '_' . $entity['post_id'];
            if (!in_array($postTag, $identities)) {
                $identities[] = $postTag;
            }
            $productTag = Product::CACHE_TAG . '_' . $entity['product_id'];
            if (!in_array($productTag, $identities)) {
                $identities[] = $productTag;
            }
        }
        return $identities;
    }
}
