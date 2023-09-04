<?php
namespace Aheadworks\BlogGraphQl\Model\TemplateFilter;

/**
 * Class Composite
 * @package Aheadworks\BlogGraphQl\Model\TemplateFilter
 */
class Composite implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(
        $filters = []
    ) {
        $this->filters = $filters;
    }

    /**
     * @inheridoc
     */
   public function filter($content)
   {
       foreach ($this->filters as $filterItem) {
           $content = $filterItem->filter($content);
       }
       return $content;
   }
}
