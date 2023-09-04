<?php
namespace Aheadworks\Blog\Model\Indexer\MultiThread;

/**
 * Interface DimensionProviderInterface
 *
 * It is created for compatibility with 2.1.X Magento
 *
 * @package Aheadworks\Blog\Model\Indexer\MultiThread
 */
interface DimensionProviderInterface extends \IteratorAggregate
{
    /**
     * Get Dimension Iterator.
     *
     * @return \Traversable
     */
    #[\ReturnTypeWillChange]
    public function getIterator();
}
