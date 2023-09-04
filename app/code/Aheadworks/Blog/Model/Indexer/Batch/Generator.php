<?php
namespace Aheadworks\Blog\Model\Indexer\Batch;

use Magento\Framework\DB\Adapter\AdapterInterface as DBAdapterInterface;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Query\BatchIteratorInterface;

/**
 * Class Generator
 */
class Generator
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var QueryGenerator
     */
    private $batchQueryGenerator;

    /**
     * @param MetadataPool $metadataPool
     * @param QueryGenerator $batchQueryGenerator
     */
    public function __construct(
        MetadataPool $metadataPool,
        QueryGenerator $batchQueryGenerator
    ) {
        $this->metadataPool = $metadataPool;
        $this->batchQueryGenerator = $batchQueryGenerator;
    }

    /**
     * Generate batch iterator for the specific entity type
     *
     * @param DBAdapterInterface $connection
     * @param string $entityType
     * @param int $batchRowCount
     * @return BatchIteratorInterface
     * @throws LocalizedException
     */
    public function getBatchIterator(
        $connection,
        $entityType,
        $batchRowCount
    ) {
        $entityMetadata = $this->metadataPool->getMetadata(
            $entityType
        );

        $select = $connection->select();
        $select->distinct(true);
        $select->from(
            [
                'e' => $entityMetadata->getEntityTable()
            ],
            $entityMetadata->getIdentifierField()
        );

        return $this->batchQueryGenerator->generate(
            $entityMetadata->getIdentifierField(),
            $select,
            $batchRowCount
        );
    }
}
