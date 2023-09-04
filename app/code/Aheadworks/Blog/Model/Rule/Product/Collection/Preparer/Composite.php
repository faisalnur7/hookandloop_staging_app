<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;

class Composite implements PreparerInterface
{
    /**
     * @var SortedPool
     */
    private $preparerSortedPool;

    /**
     * @param SortedPool $preparerSortedPool
     */
    public function __construct(
        SortedPool $preparerSortedPool
    ) {
        $this->preparerSortedPool = $preparerSortedPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($collection, $parameterList)
    {
        $preparerSortedList = $this->preparerSortedPool->getInstanceList();
        foreach ($preparerSortedList as $preparer) {
            $collection = $preparer->prepare($collection, $parameterList);
        }
        return $collection;
    }
}
