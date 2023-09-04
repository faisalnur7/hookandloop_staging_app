<?php
namespace Aheadworks\BlogGraphQl\Test\Unit\Model\ThirdPartyModule;

use Aheadworks\BlogGraphQl\Model\ThirdPartyModule\Manager;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ModuleListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleListMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->moduleListMock = $this->getMockForAbstractClass(ModuleListInterface::class);

        $this->manager = $objectManager->getObject(
            Manager::class,
            ['moduleList' => $this->moduleListMock]
        );
    }

    /**
     * Test that result is succeed when page builder module is enabled
     */
    public function testIsMagePageBuilderModuleEnabled()
    {
        $this->moduleListMock->expects($this->once())
            ->method('has')
            ->with(Manager::MAGE_PB_MODULE_NAME)
            ->willReturn(true);

        $this->assertEquals(true, $this->manager->isMagePageBuilderModuleEnabled());
    }
}
