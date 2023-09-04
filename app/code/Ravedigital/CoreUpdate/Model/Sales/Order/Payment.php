<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\Sales\Order;

//use \Ravedigital\CoreUpdate\Api\Sales\Data\OrderPayment as Orderpaymentinterface;

class Payment extends \Magento\Sales\Model\Order\Payment
{

   /* public function canCapture()
    {
        // if (!$this->getMethodInstance()->canCapture()) {
        //     return false;
        // }
        // // Check Authorization transaction state
        // $authTransaction = $this->getAuthorizationTransaction();
        // if ($authTransaction && $authTransaction->getIsClosed()) {
        //     $orderTransaction = $this->transactionRepository->getByTransactionType(
        //         Transaction::TYPE_ORDER,
        //         $this->getId(),
        //         $this->getOrder()->getId()
        //     );
        //     if (!$orderTransaction) {
        //         return false;
        //     }
        // }

        return true;
    }*/

    /**
     * Returns tax_relief_code
     *
     * @return string
     */
    /*public function getTaxReliefCode()
    {
        return $this->getData('tax_relief_code');
    }*/

    /**
     * Returns tax_relief_state
     *
     * @return string
     */
    /*public function getTaxReliefState()
    {
        return $this->getData('tax_relief_state');
    }*/

    public function getBaseShippingCaptured()
    {
        return $this->getData('tax_relief_state');
    }

     /**
     * {@inheritdoc}
     */
    /*public function setTaxReliefCode($TaxReliefCode)
    {
        return $this->setData($TaxReliefCode);
    }*/

    /**
     * {@inheritdoc}
     */
    /*public function setTaxReliefState($TaxReliefState)
    {
        return $this->setData($TaxReliefState);
    }*/
}
