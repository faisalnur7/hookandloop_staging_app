<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Blog\Model\Source\Category\Status;

/**
 * Test for \Aheadworks\Blog\Model\Source\Category\Status
 */
class StatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Status
     */
    private $sourceModel;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->sourceModel = $objectManager->getObject(Status::class);
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->sourceModel->toOptionArray()));
    }
}
