<?php
namespace Aheadworks\BlogGraphQl\Model\TemplateFilter;

/**
 * Interface FilterInterface
 * @package Aheadworks\BlogGraphQl\Model\TemplateFilter
 */
interface FilterInterface
{
    /**
     * Filter content
     *
     * @param string $content
     * @return string
     */
    public function filter($content);
}
