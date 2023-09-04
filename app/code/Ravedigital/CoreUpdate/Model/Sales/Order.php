<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\Sales;

use Magento\Sales\Api\Data\OrderInterface;

class Order extends \Magento\Sales\Model\Order
{
    /**
     * Returns fee
     *
     * @return float|null
     */
    public function getFee()
    {
        return $this->getData(OrderInterface::FEE);
    }
    
    /**
     * Returns shipping_options_method
     *
     * @return string|null
     */
    public function getShippingOptionsMethod()
    {
        return $this->getData(OrderInterface::ShippingOptionsMethod);
    }
    
    /**
     * Returns shipping_options_service
     *
     * @return string|null
     */
    public function getShippingOptionsService()
    {
        return $this->getData(OrderInterface::ShippingOptionsService);
    }
    
    /**
     * Returns shipping_options_account_number
     *
     * @return string|null
     */
    public function getShippingOptionsAccountNumber()
    {
        return $this->getData(OrderInterface::ShippingOptionsAccountNumber);
    }
    
    /**
     * Returns shipping_options_account_zip_codes
     *
     * @return string|null
     */
    public function getShippingOptionsAccountZipCodes()
    {
        return $this->getData(OrderInterface::ShippingOptionsAccountZipCodes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setFee($amount)
    {
        return $this->setData(OrderInterface::FEE, $amount);
    }
        
     /**
      * {@inheritdoc}
      */
    public function setShippingOptionsMethod($ShippingOptionsMethod)
    {
        return $this->setData(OrderInterface::ShippingOptionsMethod, $ShippingOptionsMethod);
    }
    
     /**
      * {@inheritdoc}
      */
    public function setShippingOptionsService($ShippingOptionsService)
    {
        return $this->setData(OrderInterface::ShippingOptionsService, $ShippingOptionsService);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setShippingOptionsAccountNumber($ShippingOptionsAccountNumber)
    {
        return $this->setData(OrderInterface::ShippingOptionsAccountNumber, $ShippingOptionsAccountNumber);
    }
    
     /**
      * {@inheritdoc}
      */
    public function setShippingOptionsAccountZipCodes($ShippingOptionsAccountZipCodes)
    {
        return $this->setData(OrderInterface::ShippingOptionsAccountZipCodes, $ShippingOptionsAccountZipCodes);
    }
}
