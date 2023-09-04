<?php
namespace Aheadworks\BlogGraphQl\Model\TemplateFilter;

use Aheadworks\BlogGraphQl\Model\ThirdPartyModule\PageBuilder\PageBuilderTemplateFilterFactory;
use Magento\PageBuilder\Model\Filter\Template as TemplateFilter;

/**
 * Class PageBuilder
 * @package Aheadworks\BlogGraphQl\Model\TemplateFilter
 */
class PageBuilder implements FilterInterface
{
    /**
     * @var PageBuilderTemplateFilterFactory
     */
    private $templateFilterFactory;

    /**
     * @param PageBuilderTemplateFilterFactory $templateFilterFactory
     */
    public function __construct(
        PageBuilderTemplateFilterFactory $templateFilterFactory
    ) {
        $this->templateFilterFactory = $templateFilterFactory;
    }

    /**
     * @inheridoc
     */
    public function filter($content)
    {
        /** @var  TemplateFilter|null $templateFilter */
        $templateFilter = $this->templateFilterFactory->create();

        return $templateFilter ? $templateFilter->filter($content) : $content;
    }
}
