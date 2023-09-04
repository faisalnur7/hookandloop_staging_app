<?php

namespace Exinent\TaxExempt\Block;

use Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class Sessiondata extends Template {

    protected $_coreSession;
    protected $customerSession;
    protected $customerRepositoryInterface;
    protected $_checkoutSession;

    public function __construct(
    Context $context, 
    SessionManagerInterface $coreSession,
    Session $customerSession,
    CustomerRepositoryInterface $customerRepositoryInterface,
    CheckoutSession $_checkoutSession
    ) {
        $this->_coreSession = $coreSession;
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->_checkoutSession = $_checkoutSession;
        parent::__construct($context);
    }
    
    public function getTaxregion() {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Exinent_Missing_Taxrelief.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('SessionData.php Region........');

        if($this->customerSession->getId())
        {   
           $logger->info('SessionData if Region:');
           $customer = $this->customerRepositoryInterface->getById($this->customerSession->getId());
           if(!empty($customer->getCustomAttribute('tax_exempt_state'))){
                $region = $customer->getCustomAttribute('tax_exempt_state')->getValue(); 
                $this->_coreSession->setTaxReliefState($region); 
           }else{
                $this->_coreSession->start();
                $region = $this->_coreSession->getTaxReliefState(); 
           }
           return $region;
        }else{
            $this->_coreSession->start();
            $region = $this->_coreSession->getTaxReliefState(); 
            $logger->info('SessionData Region: '.$region);
            return $region;
        }
    }
    
    public function getTaxcode() {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Exinent_Missing_Taxrelief.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('SessionData.php Taxcode........');

        if($this->customerSession->getId())
        {        
           $logger->info('SessionData if Taxcode:');
           $customer = $this->customerRepositoryInterface->getById($this->customerSession->getId());
           if(!empty($customer->getCustomAttribute('tax_exempt_number'))) {
                $taxcode = $customer->getCustomAttribute('tax_exempt_number')->getValue(); 
                $this->_coreSession->setTaxReliefCode($taxcode);
            }else{
                $this->_coreSession->start();
                $taxcode = $this->_coreSession->getTaxReliefCode();    
            }
           return $taxcode;
        }else{   
            $this->_coreSession->start();
            $taxcode = $this->_coreSession->getTaxReliefCode();
            $logger->info('SessionData Taxcode: '.$taxcode);
        }

        return $taxcode;
    }

}
