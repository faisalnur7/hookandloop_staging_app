<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst;

/**
 * Class VersionPriorTo234
 * @package Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst
 */
class VersionPriorTo234 implements DataPreparerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getData($fields)
    {
        return array_keys($fields);
    }
}