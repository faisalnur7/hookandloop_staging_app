<?php
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterfaceFactory;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Department\Save;

/**
 * Class SaveTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $model;

    /**
     * @var DepartmentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DepartmentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->departmentFactoryMock = $this->getMockBuilder(DepartmentInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->departmentRepositoryMock = $this->getMockForAbstractClass(DepartmentRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Save::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'departmentRepository' => $this->departmentRepositoryMock,
                'departmentFactory' => $this->departmentFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     *
     * @throws LocalizedException
     */
    public function testExecute()
    {
        $departmentData = [
            DepartmentInterface::NAME => 'test'
        ];
        $departmentMock = $this->getMockForAbstractClass(DepartmentInterface::class);
        $this->departmentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($departmentMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($departmentMock, $departmentData, DepartmentInterface::class)
            ->willReturn($departmentMock);
        $this->departmentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($departmentMock)
            ->willReturn($departmentMock);

        $this->model->execute($departmentData);
    }
}
