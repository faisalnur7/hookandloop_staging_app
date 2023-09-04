<?php
namespace Aheadworks\BlogGraphQl\Model\ThirdPartyModule\PageBuilder;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\BlogGraphQl\Model\ThirdPartyModule\Manager as ModuleManager;
use Magento\PageBuilder\Model\Filter\Template as TemplateFilter;

/**
 * Class PageBuilderTemplateFilterFactory
 * @package Aheadworks\BlogGraphQl\Model\ThirdPartyModule\PageBuilder
 */
class PageBuilderTemplateFilterFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Create template filter if possible
     *
     * @return TemplateFilter|null
     */
    public function create()
    {
        return $this->moduleManager->isMagePageBuilderModuleEnabled()
            ? $this->objectManager->create(TemplateFilter::class)
            : null;
    }
}
