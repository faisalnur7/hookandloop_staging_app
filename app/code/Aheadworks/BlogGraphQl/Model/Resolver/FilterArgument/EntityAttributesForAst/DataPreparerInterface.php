<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst;

/**
 * Interface DataPreparerInterface
 * @package Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst
 */
interface DataPreparerInterface
{
    /**
     * Retrieve prepared data
     *
     * @param $fields
     * @return array
     */
    public function getData($fields);
}