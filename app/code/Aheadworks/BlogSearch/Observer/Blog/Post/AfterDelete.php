<?php
namespace Aheadworks\BlogSearch\Observer\Blog\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext as FulltextPostIndexer;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class AfterDelete
 */
class AfterDelete implements ObserverInterface
{
    /**
     * @var FulltextPostIndexer
     */
    private $fulltextPostIndexer;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @param FulltextPostIndexer $fulltextPostIndexer
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        FulltextPostIndexer $fulltextPostIndexer,
        IndexerRegistry $indexerRegistry
    ) {
        $this->fulltextPostIndexer = $fulltextPostIndexer;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Process reindexing after post deleted
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var PostInterface $post */
        $post = $observer->getEvent()->getEntity();

        $indexer = $this->indexerRegistry->get(FulltextPostIndexer::INDEXER_ID);

        if (!$indexer->isScheduled() && $post) {
            $this->fulltextPostIndexer->executeRow($post->getId());
        }

        return $this;
    }
}
