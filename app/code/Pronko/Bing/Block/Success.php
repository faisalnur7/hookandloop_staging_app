<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Block;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Pronko\Bing\Api\ConfigInterface;
use Magento\Sales\Model\Order;

class Success extends Template
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Session
     */
    private $session;


    /**
     * Success constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param PriceCurrencyInterface $priceCurrencyInterface
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        PriceCurrencyInterface $priceCurrencyInterface,
        Session $session,
        array $data = []
    ) {
        $this->config = $config;
        $this->priceCurrency = $priceCurrencyInterface;
        $this->session = $session;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return json_encode([
            'amount' => $this->getAmount()
        ]);
    }

    /**
     * @return float|int
     */
    private function getAmount()
    {
        /** @var Order $order */
        $order = $this->session->getLastRealOrder();
        return round(
            $this->_storeManager->getStore()
                ->getBaseCurrency()
                ->convert($order->getBaseGrandTotal(), $this->config->getConvertToCurrency()),
            PriceCurrencyInterface::DEFAULT_PRECISION
        );
    }
}
