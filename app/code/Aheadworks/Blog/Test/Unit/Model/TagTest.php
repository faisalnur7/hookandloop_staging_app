<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Model\Tag;
use Aheadworks\Blog\Model\ResourceModel\Validator\TagNameIsUnique;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Tag
 */
class TagTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tag
     */
    private $tagModel;

    /**
     * @var TagNameIsUnique|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagNameIsUniqueMock;

    /**
     * Tag model data
     *
     * @var array
     */
    private $tagData = ['name' => 'tag'];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);
        $this->tagNameIsUniqueMock = $this->getMockBuilder(TagNameIsUnique::class)
            ->setMethods(['validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->tagModel = $objectManager->getObject(
            Tag::class,
            ['tagNameIsUnique' => $this->tagNameIsUniqueMock]
        );
    }

    /**
     * Testing that proper exceptions are thrown if tag data is incorrect
     *
     * @dataProvider validateBeforeSaveDataProvider
     * @param array $tagData
     * @param string $exceptionMessage
     */
    public function testValidateBeforeSaveExceptions($tagData, $exceptionMessage)
    {
        $this->tagNameIsUniqueMock->expects($this->any())
            ->method('validate')
            ->with($this->equalTo($this->tagModel))
            ->willReturn(true);
        $this->tagModel->setData($tagData);
        try {
            $this->tagModel->validateBeforeSave();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Magento\Framework\Validator\Exception::class, $e);
            $this->assertEquals($exceptionMessage, $e->getMessage());
        }
    }

    /**
     * Testing that proper exception is thrown if tag data contains duplicated name
     */
    public function testValidateBeforeSaveDuplicatedName()
    {
        $this->tagNameIsUniqueMock->expects($this->any())
            ->method('validate')
            ->with($this->equalTo($this->tagModel))
            ->willReturn(false);
        $this->tagModel->setData($this->tagData);

        $this->expectException(\Magento\Framework\Validator\Exception::class);
        $this->tagModel->validateBeforeSave();
    }

    /**
     * @return array
     */
    public function validateBeforeSaveDataProvider()
    {
        return [
            'empty name' => [
                array_merge($this->tagData, ['name' => '']),
                'Empty tags are not allowed.'
            ]
        ];
    }
}
