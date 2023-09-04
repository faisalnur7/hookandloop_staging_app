<?php
namespace Aheadworks\Blog\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\PageBuilder\Model\Config;
use Aheadworks\Blog\Model\ThirdPartyModule\Manager as ThirdPartyModuleManager;

/**
 * Class PageBuilderConfigFactory
 * @package Aheadworks\Blog\Model
 */
class PageBuilderConfigFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * PageBuilderConfigFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ThirdPartyModuleManager $thirdPartyModuleManager
    ) {
        $this->objectManager = $objectManager;
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * Create page builder config factory instance
     *
     * @return Config|null
     */
    public function create()
    {
        $result = null;

        if ($this->thirdPartyModuleManager->isMagePageBuilderModuleEnabled()) {
            $result = $this->objectManager->create(Config::class);
        }

        return $result;
    }
}
