<?php

namespace Exinent\TaxExempt\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Session\SessionManagerInterface;

class SetOrderTaxExempt implements ObserverInterface {

    protected $_coreSession;

    public function __construct(
    SessionManagerInterface $coreSession
    ) {
        $this->_coreSession = $coreSession;
    }

    public function execute(EventObserver $observer) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Exinent_Missing_Taxrelief.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('------Start SetOrderTaxExempt observer........');      

        $payment = $observer->getQuote()->getPayment();
        $taxcode = $payment->getTaxReliefCode();
        $region = $payment->getTaxReliefState();
        $logger->info('Payment Taxcode -> '.$taxcode);
        $logger->info('Payment Region -> '.$region);

        if (!empty($taxcode) && $region != 'Please select region, state or province') {
            $orderPayment = $observer->getOrder()->getPayment();
            $orderPayment->setTaxReliefCode($taxcode);
            $orderPayment->setTaxReliefState($region);
        }
        $logger->info('Session Taxcode -> '.$this->_coreSession->getTaxReliefCode());
        $logger->info('Session Region -> '.$this->_coreSession->getTaxReliefState());
       // $this->_coreSession->start();
       // $this->_coreSession->unsTaxReliefCode();
       // $this->_coreSession->unsTaxReliefState();
        $logger->info('------End SetOrderTaxExempt observer........');      
        return $this;
    }

}
