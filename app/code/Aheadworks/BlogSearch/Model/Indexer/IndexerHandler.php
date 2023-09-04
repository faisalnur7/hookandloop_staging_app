<?php
namespace Aheadworks\BlogSearch\Model\Indexer;

use Magento\Elasticsearch\Model\Adapter\Elasticsearch as ElasticsearchAdapter;
use Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver;
use Magento\Elasticsearch\Model\Indexer\IndexStructure;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;

/**
 * Indexer Handler for Elasticsearch engine.
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * Size of default batch
     */
    const DEFAULT_BATCH_SIZE = 500;

    /**
     * @var ElasticsearchAdapter
     */
    private $adapter;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var IndexNameResolver
     */
    private $indexNameResolver;

    /**
     * @var IndexStructure
     */
    private $indexStructure;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var string
     */
    private $indexerId;

    /**
     * IndexerHandler constructor.
     * @param ElasticsearchAdapter $adapter
     * @param ScopeResolverInterface $scopeResolver
     * @param IndexNameResolver $indexNameResolver
     * @param IndexStructure $indexStructure
     * @param Batch $batch
     * @param string $indexerId
     * @param int $batchSize
     */
    public function __construct(
        ElasticsearchAdapter $adapter,
        ScopeResolverInterface $scopeResolver,
        IndexNameResolver $indexNameResolver,
        IndexStructure $indexStructure,
        Batch $batch,
        string $indexerId,
        $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->adapter = $adapter;
        $this->scopeResolver = $scopeResolver;
        $this->indexNameResolver = $indexNameResolver;
        $this->indexStructure = $indexStructure;
        $this->batch = $batch;
        $this->indexerId = $indexerId;
        $this->batchSize = $batchSize;
    }

    /**
     * @inheritdoc
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        foreach ($this->batch->getItems($documents, $this->batchSize) as $documentsBatch) {
            $this->adapter->addDocs($documentsBatch, $scopeId, $this->getIndexerId());
        }
        $this->adapter->updateAlias($scopeId, $this->getIndexerId());
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        $documentIds = [];
        foreach ($documents as $document) {
            $documentIds[$document] = $document;
        }
        $this->adapter->deleteDocs($documentIds, $scopeId, $this->getIndexerId());
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cleanIndex($dimensions)
    {
        $this->indexStructure->delete($this->getIndexerId(), $dimensions);
        $this->indexStructure->create($this->getIndexerId(), [], $dimensions);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable($dimensions = [])
    {
        return $this->adapter->ping();
    }

    /**
     * Returns indexer id.
     *
     * @return string
     */
    private function getIndexerId()
    {
        return $this->indexNameResolver->getIndexMapping($this->indexerId);
    }
}
