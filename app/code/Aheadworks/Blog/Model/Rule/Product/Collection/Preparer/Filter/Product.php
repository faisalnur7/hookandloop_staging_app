<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer\Filter;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;

class Product implements PreparerInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare($collection, $parameterList)
    {
        $productsFilter = $parameterList[PreparerInterface::PRODUCTS_FILTER_KEY] ?? null;
        if ($productsFilter) {
            $collection->addIdFilter($productsFilter);
        }
        return $collection;
    }
}
