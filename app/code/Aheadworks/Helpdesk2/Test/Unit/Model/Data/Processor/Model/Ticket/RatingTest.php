<?php
namespace Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Model\Ticket;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\CollectorComposite;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Rating;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class RatingTest
 *
 * @package Aheadworks\Helpdesk2\Test\Unit\Model\Data\Processor\Model\Ticket
 */
class RatingTest extends TestCase
{
    /**
     * @var Rating
     */
    private $model;

    /**
     * @var CollectorComposite|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectorCompositeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->collectorCompositeMock = $this->getMockBuilder(CollectorComposite::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            Rating::class,
            [
                'collectorComposite' => $this->collectorCompositeMock,
            ]
        );
    }

    /**
     * Testing of prepareModelBeforeSave method
     */
    public function testPrepareModelBeforeSave()
    {
        $ticketMock = $this->getMockForAbstractClass(TicketInterface::class);
        $ticketMock->expects($this->any())
            ->method('setRating')
            ->with(0)
            ->willReturnSelf();
        $ticketMock->expects($this->once())
            ->method('getStatusId')
            ->willReturn(TicketStatus::NEW);
        $this->collectorCompositeMock->expects($this->once())
            ->method('collect')
            ->with($ticketMock)
            ->willReturnSelf();

        $this->assertSame($ticketMock, $this->model->prepareModelBeforeSave($ticketMock));
    }
}
