<?php
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Department\Delete;

/**
 * Class DeleteTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Department
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private $model;

    /**
     * @var DepartmentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $departmentRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->departmentRepositoryMock = $this->getMockForAbstractClass(DepartmentRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            Delete::class,
            [
                'departmentRepository' => $this->departmentRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Department ID param is required to delete');

        $this->model->execute(
            [
                DepartmentInterface::ID => null
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
        $departmentId = 2;
        $result = true;
        $departmentMock = $this->getMockForAbstractClass(DepartmentInterface::class);
        $this->departmentRepositoryMock->expects($this->once())
            ->method('get')
            ->with($departmentId)
            ->willReturn($departmentMock);
        $this->departmentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($departmentMock)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->model->execute(
                [
                    DepartmentInterface::ID => $departmentId
                ]
            )
        );
    }
}
