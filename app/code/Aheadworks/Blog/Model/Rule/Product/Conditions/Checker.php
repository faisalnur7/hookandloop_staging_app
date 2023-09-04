<?php
namespace Aheadworks\Blog\Model\Rule\Product\Conditions;

use Magento\CatalogRule\Model\Rule\Condition\Combine as CatalogRuleConditionCombine;

class Checker
{
    /**
     * Check if it is possible to apply conditions to the product collection for loading optimization
     *
     * @param CatalogRuleConditionCombine $conditions
     * @return bool
     */
    public function canApplyConditionsToProductCollection($conditions)
    {
        if (!$conditions || !$conditions->getConditions()) {
            return false;
        }

        return true;
    }
}
