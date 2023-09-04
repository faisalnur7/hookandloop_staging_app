<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pronko\Bing\Block\Success;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class SuccessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Success
     */
    private $object;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var PriceCurrencyInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->config = $this->createMock(\Pronko\Bing\Model\Config::class);
        $this->priceCurrency = $this->createMock(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        $this->storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $context = $this->createMock(\Magento\Framework\View\Element\Template\Context::class);
        $context->expects($this->once())
            ->method('getStoreManager')
            ->willReturn($this->storeManager);
        $this->object = $objectManager->getObject(
            \Pronko\Bing\Block\Success::class,
            [
                'config' => $this->config,
                'orderProvider' => $this->orderProvider,
                'priceCurrencyInterface' => $this->priceCurrency,
                'context' => $context
            ]
        );
    }

    public function testGetOptions()
    {
        $expectedResult = json_encode(['amount' => 109.71]);

        $order = $this->createMock(\Magento\Sales\Model\Order::class);
        $currency = $this->createMock(\Magento\Directory\Model\Currency::class);
        $store = $this->createMock(\Magento\Store\Model\Store::class);

        $this->orderProvider->expects($this->once())
            ->method('getOrders')
            ->willReturn([$order]);

        $order->expects($this->once())
            ->method('getBaseSubtotal')
            ->willReturn(126.99);

        $order->expects($this->once())
            ->method('getBaseDiscountAmount')
            ->willReturn(-3.00);

        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($store);

        $store->expects($this->once())
            ->method('getBaseCurrency')
            ->willReturn($currency);

        $this->config->expects($this->once())
            ->method('getConvertToCurrency')
            ->willReturn('EUR');

        $currency->expects($this->once())
            ->method('convert')
            ->willReturn(109.71);

        $this->assertEquals($expectedResult, $this->object->getOptions());
    }
}
