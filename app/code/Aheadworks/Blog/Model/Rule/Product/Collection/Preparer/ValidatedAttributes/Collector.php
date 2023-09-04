<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer\ValidatedAttributes;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;
use Magento\CatalogRule\Model\Rule\Condition\Combine as CatalogRuleConditionCombine;

class Collector implements PreparerInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare($collection, $parameterList)
    {
        $conditions = $parameterList[PreparerInterface::CONDITIONS_KEY] ?? null;
        if ($conditions instanceof CatalogRuleConditionCombine) {
            $conditions->collectValidatedAttributes($collection);
        }
        return $collection;
    }
}
