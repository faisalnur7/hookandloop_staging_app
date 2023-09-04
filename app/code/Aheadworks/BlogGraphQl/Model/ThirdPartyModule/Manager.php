<?php
namespace Aheadworks\BlogGraphQl\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Manager
 * @package Aheadworks\BlogGraphQl\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Magento Page Builder module name
     */
    const MAGE_PB_MODULE_NAME = 'Magento_PageBuilder';

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
     * Check if magento page builder module enabled
     *
     * @return bool
     */
    public function isMagePageBuilderModuleEnabled()
    {
        return $this->moduleList->has(self::MAGE_PB_MODULE_NAME);
    }
}
