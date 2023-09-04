<?php

namespace Exinent\TaxExempt\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Session\SessionManagerInterface;
use \Magento\Catalog\Model\ProductFactory; 

class Totalsbefore implements ObserverInterface {

    protected $_coreSession;
    protected $_productloader;

    public function __construct(
    SessionManagerInterface $coreSession, ProductFactory $_productloader
    ) {
        $this->_coreSession = $coreSession;
        $this->_productloader = $_productloader;
    }

    public function execute(Observer $observer) {
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Exinent_Missing_Taxrelief.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('.......Start Totalsbefore.php..................');

        $this->_coreSession->start();
        $taxcode = $this->_coreSession->getTaxReliefCode();
        $region = $this->_coreSession->getTaxReliefState(); 
         $logger->info('Totalsbefore.php Taxcode: '.$taxcode);
          $logger->info('Totalsbefore.php Region: '.$region);

        $quote = $observer->getQuote();
        $payment = $quote->getPayment();
        
        if(!empty($taxcode) && $region!='Please select region, state or province') {
            $items = $quote->getAllVisibleItems();
            
            foreach($items as $item){
                if ($item->getProductType() == 'configurable') {
                    $item->getProduct()->setTaxClassId(0);
                }
                else{
                    $item->getProduct()->setTaxClassId(0);
                }
            }
            $logger->info('Totalsbefore.php If ..................');
            $payment->setTaxReliefState($region);
            $payment->setTaxReliefCode($taxcode);
            $quote->save();
        }
        else {            
            $logger->info('Totalsbefore.php Else..................');
            $items = $quote->getAllVisibleItems();
            
            foreach($items as $item){
                if(!$item->getProduct()->getTaxClassId()) {
                    $product = $this->_productloader->create()->load($item->getProduct()->getId());
                    $item->getProduct()->setTaxClassId($product->getTaxClassId());
                }
            }
            $payment->setTaxReliefState('');
            $payment->setTaxReliefCode('');
            $quote->save();
        }
        $logger->info('.......End Totalsbefore.php..................');
        return $this;
    }

}
