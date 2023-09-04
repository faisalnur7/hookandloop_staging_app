<?php
namespace Aheadworks\BlogSearch\Observer\Blog\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext as FulltextPostIndexer;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class AfterMassUpdatePosts
 */
class AfterMassUpdatePosts implements ObserverInterface
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
     * Process reindexing after post saved
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var PostInterface[] $posts */
        $posts = $observer->getEvent()->getEntities();
        $indexer = $this->indexerRegistry->get(FulltextPostIndexer::INDEXER_ID);

        $postsIds = [];
        foreach ($posts as $post) {
            $postsIds[] = $post->getId();
        }

        if (!$indexer->isScheduled() && !empty($postsIds)) {
            $this->fulltextPostIndexer->executeList($postsIds);
        }

        return $this;
    }
}
