<?php
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Close;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class CloseTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Command\Ticket
 */
class CloseTest extends TestCase
{
    /**
     * @var Close
     */
    private $model;

    /**
     * @var TicketManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ticketManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->ticketManagementMock = $this->getMockForAbstractClass(TicketManagementInterface::class);
        $this->model = $objectManager->getObject(
            Close::class,
            [
                'ticketManagement' => $this->ticketManagementMock,
            ]
        );
    }

    /**
     * Testing of execute method on exception
     *
     * @throws LocalizedException
     */
    public function testExecuteOnException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Ticket is required to be closed');

        $this->model->execute(
            [
                'ticket' => null
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
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketMock->expects($this->once())
            ->method('setStatusId')
            ->with(TicketStatus::CLOSED)
            ->willReturnSelf();

        $this->assertSame(
            $ticketMock,
            $this->model->execute(
                [
                    'ticket' => $ticketMock
                ]
            )
        );
    }
}
