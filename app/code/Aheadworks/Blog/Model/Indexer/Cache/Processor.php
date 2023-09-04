<?php
namespace Aheadworks\Blog\Model\Indexer\Cache;

use Aheadworks\Blog\Model\Post;
use Magento\Framework\Indexer\CacheContext as IndexerCacheContext;
use Magento\Framework\Indexer\CacheContextFactory as IndexerCacheContextFactory;
use Aheadworks\Blog\Model\Indexer\Cache\Processor\Cleaner as IndexerCacheCleaner;
use Magento\Catalog\Model\Product as ProductModel;

/**
 * Class Processor
 */
class Processor
{
    /**
     * @var IndexerCacheContextFactory
     */
    private $indexerCacheContextFactory;

    /**
     * @var IndexerCacheCleaner
     */
    private $indexerCacheCleaner;

    /**
     * @param IndexerCacheContextFactory $indexerCacheContextFactory
     * @param IndexerCacheCleaner $indexerCacheCleaner
     */
    public function __construct(
        IndexerCacheContextFactory $indexerCacheContextFactory,
        IndexerCacheCleaner $indexerCacheCleaner
    ) {
        $this->indexerCacheContextFactory = $indexerCacheContextFactory;
        $this->indexerCacheCleaner = $indexerCacheCleaner;
    }

    /**
     * Update the cache after full reindex
     *
     * @return $this
     */
    public function processFullReindex()
    {
        /** @var IndexerCacheContext $indexerCacheContext */
        $indexerCacheContext = $this->indexerCacheContextFactory->create();
        $indexerCacheContext->registerTags(
            [
                ProductModel::CACHE_TAG,
                Post::CACHE_TAG
            ]
        );
        $this->indexerCacheCleaner->execute(
            $indexerCacheContext
        );
        return $this;
    }
}