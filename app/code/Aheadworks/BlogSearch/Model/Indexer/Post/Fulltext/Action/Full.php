<?php
namespace Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext\Action;

use Aheadworks\Blog\Api\Data\PostInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\BlogSearch\Model\Converter\PostToEsDocument as PostToEsDocumentConverter;

/**
 * Class Full
 */
class Full
{
    /**
     * @var DataProvider
     */
    private $postDataProvider;

    /**
     * @var PostToEsDocumentConverter
     */
    private $postToEsDocumentConverter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Full constructor.
     * @param DataProvider $postDataProvider
     * @param PostToEsDocumentConverter $postToEsDocumentConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataProvider $postDataProvider,
        PostToEsDocumentConverter $postToEsDocumentConverter,
        LoggerInterface $logger
    ) {
        $this->postDataProvider = $postDataProvider;
        $this->postToEsDocumentConverter = $postToEsDocumentConverter;
        $this->logger = $logger;
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int[] $postIds
     * @return \Generator
     */
    public function rebuildStoreIndex($storeId, $postIds = null)
    {
        try {
            /** @var PostInterface[] $searchablePosts */
            $searchablePosts = $this->postDataProvider->getSearchablePosts($storeId, $postIds);
        } catch (LocalizedException $e) {
            $this->logger->error($e);
        }

        foreach ($searchablePosts as $post) {
            yield $post->getId() => $this->postToEsDocumentConverter->convert($post);
        }
    }
}
