<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pronko\Bing\Block\Tag;

class TagTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tag
     */
    private $object;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->config = $this->createMock(\Pronko\Bing\Model\Config::class);
        $this->object = $objectManager->getObject(
            \Pronko\Bing\Block\Tag::class,
            [
                'config' => $this->config
            ]
        );
    }

    public function testGetTagId()
    {
        $expectedResult = '123456';

        $this->config->expects($this->once())
            ->method('getTagId')
            ->willReturn('123456');

        $this->assertEquals($expectedResult, $this->object->getTagId());
    }
}
