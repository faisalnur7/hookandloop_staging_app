<?php
namespace Aheadworks\Blog\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Manager
 * @package Aheadworks\Blog\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Magento Page Builder module name
     */
    const MAGE_PAGE_BUILDER_MODULE_NAME = 'Magento_PageBuilder';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Magento Page Builder module enabled
     *
     * @return bool
     */
    public function isMagePageBuilderModuleEnabled()
    {
        return $this->moduleList->has(self::MAGE_PAGE_BUILDER_MODULE_NAME);
    }
}
