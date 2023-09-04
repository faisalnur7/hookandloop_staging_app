<?php

/**
 * Exinent_Checkout Module 
 *
 * @category    checkout
 * @package     Exinent_Checkout
 * @author      pawan
 *
 */

namespace Exinent\Checkout\Model\Type;

class Onepage extends \Magento\Checkout\Model\Type\Onepage {

    public function duplicateOrder($order)
    {

        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
            break;
            case self::METHOD_REGISTER:
            $isNewCustomer = true;
            break;
            default:
            break;
        }

        if ($isNewCustomer) {
            try {

                $this->_involveNewCustomer();
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        
        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
        ->setLastSuccessQuoteId($this->getQuote()->getId())
        ->clearHelperData();

        if ($order) {
            $redirectUrl = '';
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if ($order->getCanSendNewEmailFlag()) {
                try {
                    $this->orderSender->send($order);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
            ->setRedirectUrl($redirectUrl)
            ->setLastRealOrderId($order->getIncrementId());
        }
        $quote = $this->getQuote();
        $quote->setIsActive(false);
        $quote->save();
        return $this;
    }
}
