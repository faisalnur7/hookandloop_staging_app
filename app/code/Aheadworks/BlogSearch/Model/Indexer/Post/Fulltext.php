<?php
namespace Aheadworks\BlogSearch\Model\Indexer\Post;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Store\Model\StoreDimensionProvider;
use Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext\Action\Full as FullAction;

/**
 * Class Fulltext
 */
class Fulltext implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'aheadworks_blogsearch_post_fulltext';

    /**
     * @var StoreDimensionProvider
     */
    private $storeDimensionProvider;

    /**
     * @var IndexerInterface
     */
    private $indexerHandler;

    /**
     * @var FullAction
     */
    private $fullAction;

    /**
     * @param StoreDimensionProvider $storeDimensionProvider
     * @param IndexerInterface $indexerHandler
     * @param FullAction $fullAction
     */
    public function __construct(
        StoreDimensionProvider $storeDimensionProvider,
        IndexerInterface $indexerHandler,
        FullAction $fullAction
    ) {
        $this->storeDimensionProvider = $storeDimensionProvider;
        $this->indexerHandler = $indexerHandler;
        $this->fullAction = $fullAction;
    }

    /**
     * @inheritdoc
     */
    public function execute($postIds)
    {
        foreach ($this->storeDimensionProvider->getIterator() as $storeDimension) {
            $storeId = current($storeDimension)->getValue();

            if ($this->indexerHandler->isAvailable($storeDimension)) {
                $this->indexerHandler->deleteIndex($storeDimension, new \ArrayIterator($postIds));
                $this->indexerHandler->saveIndex($storeDimension, $this->fullAction->rebuildStoreIndex($storeId, $postIds));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function executeFull()
    {
        foreach ($this->storeDimensionProvider->getIterator() as $storeDimension) {
            $storeId = current($storeDimension)->getValue();

            $this->indexerHandler->cleanIndex($storeDimension);
            $this->indexerHandler->saveIndex($storeDimension, $this->fullAction->rebuildStoreIndex($storeId));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function executeList(array $postIds)
    {
        $this->execute($postIds);
    }

    /**
     * {@inheritDoc}
     */
    public function executeRow($postId)
    {
        $this->execute([$postId]);
    }
}
