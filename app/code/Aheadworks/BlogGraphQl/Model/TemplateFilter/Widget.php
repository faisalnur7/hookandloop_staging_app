<?php
namespace Aheadworks\BlogGraphQl\Model\TemplateFilter;

use Magento\Widget\Model\Template\FilterEmulate;

/**
 * Class Widget
 * @package Aheadworks\BlogGraphQl\Model\TemplateFilter
 */
class Widget implements FilterInterface
{
    /**
     * @var FilterEmulate
     */
    private $filterEmulate;

    /**
     * @param FilterEmulate $filterEmulate
     */
    public function __construct(
        FilterEmulate $filterEmulate
    ) {
        $this->filterEmulate = $filterEmulate;
    }

    /**
     * @inheridoc
     */
    public function filter($content)
    {
        return $this->filterEmulate->filter($content);
    }
}
