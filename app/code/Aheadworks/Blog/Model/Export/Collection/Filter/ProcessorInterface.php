<?php
namespace Aheadworks\Blog\Model\Export\Collection\Filter;

use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * The process of applying filters to a collection
     *
     * @param AbstractCollection $collection
     * @param string $columnName
     * @param array|string $value
     * @param null|string $type
     * @return void
     */
    public function process(AbstractCollection $collection, $columnName, $value, $type = null);
}