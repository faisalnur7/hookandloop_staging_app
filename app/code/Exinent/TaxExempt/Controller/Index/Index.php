<?php

namespace Exinent\TaxExempt\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\Customer; 
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\Session;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $_coreSession;
    protected $customer;
    protected $customerFactory;    
    protected $customerSession;

    public function __construct(
    Context $context, 
    PageFactory $resultPageFactory, 
    JsonFactory $resultJsonFactory, 
    SessionManagerInterface $coreSession,
    Customer $customer,
    CustomerFactory $customerFactory,
    Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_coreSession = $coreSession;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    public function execute() {

        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Exinent_Missing_Taxrelief.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-------------Start Index.php.......');

        $taxcode = $this->getRequest()->getParam('tax');
        $region = $this->getRequest()->getParam('region');
        
        if (($taxcode !== '' ) && ($region !== 'Please select region, state or province' ) && (preg_match("/[0-9][-]*$/", $taxcode)) && strlen($taxcode) >= 6) {

            $logger->info('Set Taxcode -> '.$taxcode);
            $logger->info('Set Region -> '.$region);

            $this->_coreSession->start();
            $this->_coreSession->setTaxReliefCode($taxcode);
            $this->_coreSession->setTaxReliefState($region);
            $result->setData([ 'taxcode' => $taxcode, 'region' => $region, 'result' => 'Tax Exempted']);

            if($this->customerSession->getId())
            {
                $customerId = $this->customerSession->getId();
                $customer = $this->customer->load($customerId);
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('tax_exempt_number',$taxcode);
                $customerData->setCustomAttribute('tax_exempt_state',$region);
                $customer->updateData($customerData);
                $customerResource = $this->customerFactory->create();
                $customerResource->saveAttribute($customer, 'tax_exempt_number');
                $customerResource->saveAttribute($customer, 'tax_exempt_state');
            }

        } else {
            $logger->info('Tax Code or State not selected ');
            $this->_coreSession->start();
            $this->_coreSession->unsTaxReliefCode();
            $this->_coreSession->unsTaxReliefState();
            $result->setData([ 'taxcode' => $taxcode, 'region' => $region, 'result' => 'Tax Not Exempted']);
        }
        $logger->info('----------End Index.php.......');

        return $result;
    }

}
