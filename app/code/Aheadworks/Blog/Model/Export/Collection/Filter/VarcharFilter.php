<?php
namespace Aheadworks\Blog\Model\Export\Collection\Filter;

use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;

/**
 * Class VarcharFilter
 */
class VarcharFilter implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(AbstractCollection $collection, $columnName, $value, $type = null)
    {
        $collection->addFieldToFilter($columnName, ['like' => '%' . $value . '%']);
    }
}