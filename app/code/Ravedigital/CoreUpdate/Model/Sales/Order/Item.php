<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\Sales\Order;

use Magento\Sales\Api\Data\OrderItemInterface;

class Item extends \Magento\Sales\Model\Order\Item
{


    /* Returns handling_charges
     *
     * @return float|null
     */
    public function getHandlingCharges()
    {
        //if($this->getData(OrderItemInterface::PRODUCT_TYPE) == 'simple'){
            return $this->getData(OrderItemInterface::HANDLING_CHARGES);
        //}
    }
     /**
      * Returns cut_to_length_charges
      *
      * @return float|null
      */
    public function getCutToLengthCharges()
    {
        return $this->getData('cut_to_length_charges');
    }

    /**
     * {@inheritdoc}
     */
    public function setHandlingCharges($handlingCharges)
    {
        //if($this->getData(OrderItemInterface::PRODUCT_TYPE) == 'simple'){
            return $this->setData(OrderItemInterface::HANDLING_CHARGES, $handlingCharges);
        //}
    }

    /**
     * {@inheritdoc}
     */
    public function setCutToLengthCharges($cutToLengthCharges)
    {
        $this->setData('cut_to_length_charges', $cutToLengthCharges);
        $this->save();
        return true;
        //return $this->setData('cut_to_length_charges', $cutToLengthCharges)->save;
    }
}
