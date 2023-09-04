<?php
namespace Ravedigital\Ordercreate\Controller\Adminhtml\Missingorder;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteCollectionFactory;

class Submitorder extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_messageManager;
    protected $quoteFactory;
    protected $quoteCollectionFactory;
    protected $orderFactory;


    public function __construct(
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        QuoteFactory $quoteFactory,
        QuoteCollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Context $context
    ) { 
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->_messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    public function execute()
    { 
        $post = $this->getRequest()->getPostValue();
        $orderIds= explode (",", $post['missing_orderid']);  

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $websiteId = $storeManager->getStore()->getWebsiteId();     
          
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
 
        $_paymentMethod = 'checkmo';
      
        foreach ($orderIds as $missingOrderId)
        {  
            $missingOrderId = trim($missingOrderId);

            $order = $this->orderFactory->create()->loadByIncrementId($missingOrderId);
            if($order->getEntityID() == NULL )
            {        
          
                $selectQuoteId = $connection->select()
                    ->FROM(
                    ['ce' => 'quote'],
                    ['ce.entity_id']
                    )->WHERE ('ce.reserved_order_id = ?', $missingOrderId);
                $orderQuote = $connection->fetchAll($selectQuoteId);
                if(isset($orderQuote) && !empty($orderQuote))
                {
                    $quoteId =  $orderQuote[0]['entity_id'];
                    $quote = $this->quoteFactory->create()->load($quoteId );  
                    $quoteEmailAddress =  $quote->getCustomerEmail();

                   try {
                        if(isset($quoteEmailAddress) && !empty($quoteEmailAddress)){

                            $customer = $objectManager->get('Magento\Customer\Model\Customer')->setWebsiteId($websiteId)->loadByEmail($quoteEmailAddress);

                            // Get shipping address Id from Quote Id                         
                            $connection = $resource->getConnection();
                            $selectAddressId = $connection->select()
                                ->FROM(
                                    ['ce' => 'quote_address'],
                                    ['address_id']
                                )->WHERE ('ce.quote_id = ?', $quoteId)->WHERE ('ce.address_type = ?','shipping');
                            $orderData = $connection->fetchAll($selectAddressId);

                            if(isset($orderData) && $orderData !='')
                            {     
                                $isValidQuote = true;
                                $address_id = $orderData[0]['address_id']; 
                                $missingFields = '';

                                // Get shipping method from shipping address Id 
                                $quoteAddress = $objectManager->get('Magento\Quote\Model\Quote\Address')->load($address_id);
                                $shipAddress = $quoteAddress->getData();
                                $shippingMethod = $shipAddress['shipping_method'];
                                if($shippingMethod ==''){$shippingMethod = 'freeshipping_freeshipping';}
                                
                                // Set Shipping address
                                $shipInfo = $quote->getShippingAddress();
                                $shipping_data = $shipInfo->getOrigData(); 
                               // var_dump($shipping_data); die;
                                if($shipping_data['firstname']==''){$shipping_data['firstname']='Guest';}
                                if($shipping_data['lastname']==''){$shipping_data['lastname']='User';}
                                if($shipping_data['street']==''){$isValidQuote = false; $missingFields = 'Street, ';}
                                if($shipping_data['country_id']==''){$isValidQuote = false; $missingFields = 'Country, ';}
                                if($shipping_data['region']==''){$isValidQuote = false; $missingFields = 'Region, ';}
                                if($shipping_data['region_id']==''){$isValidQuote = false; $missingFields = 'Region_id, ';}    
                                if($shipping_data['postcode']==''){$isValidQuote = false; $missingFields = 'Postcode, ';}
                               // if($shipping_data['telephone']==''){$isValidQuote = false; $missingFields = 'Telephone, ';}
                                $quote->getShippingAddress()->addData($shipping_data);

                                // Set Billing address
                                $billnfo = $quote->getBillingAddress();
                                $billing_data = $billnfo->getOrigData(); 
                                if($billing_data['firstname']==''){$billing_data['firstname']='Guest';}
                                if($billing_data['lastname']==''){$billing_data['lastname']='User';}
                                    if($billing_data['street']==''){$isValidQuote = false;}
                                if($billing_data['country_id']==''){$isValidQuote = false;}
                                if($billing_data['region']==''){$isValidQuote = false;}
                                if($billing_data['region_id']==''){$isValidQuote = false;}    
                                if($billing_data['postcode']==''){$isValidQuote = false;}
                               // if($billing_data['telephone']==''){$isValidQuote = false;}
                                $quote->getBillingAddress()->addData($billing_data);
                               
                                //check if shipping exist

                                if (!$shippingMethod) {  
                                    $isValidQuote = false;
                                    $missingFields = 'Shipping Method, ';
                                }

                               //check if payment exist          
                                if (!$quote->getPayment()->getMethod()) {
                                    $isValidQuote = false;
                                    $missingFields = 'Payment Method, ';
                                }


                                if ($isValidQuote) { 
                                    $shipData = $quote->getShippingAddress()
                                      ->setCollectShippingRates(true)
                                      ->setShippingMethod($shippingMethod)
                                      ->collectShippingRates()->save();

                                    $quote->setPaymentMethod($_paymentMethod); //payment method, please verify checkmo must 
                                    // Set Sales Order Payment, We have taken check/money order
                                    $quote->getPayment()->importData(['method' => $_paymentMethod]);
                                     
                                    $quote->collectTotals()->save();

                                    // Check customer is registered or Guest
                                    $customer = $objectManager->get('Magento\Customer\Model\Customer')->setWebsiteId($websiteId)->loadByEmail($quoteEmailAddress);
                                  
                                    if (!$customer->getId()) {                  
                                        $quote->setCustomerId(null);
                                        $quote->setCustomerEmail($quoteEmailAddress);
                                        $quote->setCustomerIsGuest(true);
                                        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
                                    }else{ 
                                    $customerRepo = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer->getEntityId());
                                    $quote->assignCustomer($customerRepo); 
                                    }  

                                    // Create Order From Quote Object                            
                                    $order = $objectManager->create('Magento\Quote\Model\QuoteManagement')->submit($quote); 
                                    $order->addStatusHistoryComment(__('This order is generated in reference of missing order ID #').$missingOrderId)->setIsCustomerNotified(false);
                                    $order->save();

                                    if (!$order) { 
                                        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/createorder.log');
                                        $logger = new \Zend\Log\Logger();
                                        $logger->addWriter($writer);
                                        $logger->info('Something went wrong, Unable to create order. Order Id: '.$missingOrderId);
                                        $this->_messageManager->addNotice('Something went wrong, Unable to create order. Order Id #'.$missingOrderId);
                                    } else {
                                        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/createorder.log');
                                        $logger = new \Zend\Log\Logger();
                                        $logger->addWriter($writer);
                                        $logger->info('Order has been placed for missing order Id#: '.$missingOrderId);
                                        $this->_messageManager->addSuccess('Order has been placed for missing order Id #'.$missingOrderId);
                                    }
                                } else {
                                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/createorder.log');
                                    $logger = new \Zend\Log\Logger();
                                    $logger->addWriter($writer);
                                    $fieldMessage = 'The quote is not valid for missing order Id #'.$missingOrderId.'<br> Following fields value are missing: '. $missingFields;
                                    if(substr($fieldMessage, -2) == ', '){
                                      $fieldMessage =  substr($fieldMessage, 0, -2);
                                    }
                                    $this->_messageManager->addNotice($fieldMessage);
                                }    
                            }else{continue;}
                        }// if quoteEmailAddress
                        else{
                            $this->_messageManager->addNotice('The email address is missing for order Id #'.$missingOrderId);                    
                        }
                    } catch (Exception $e) { 
                        $this->_messageManager->addError('Order Id #'.$missingOrderId.' '.$e->getMessage());
                    }

                }//if isset
                else{
                    $this->_messageManager->addNotice('This order Id #'.$missingOrderId.' not exist!'); 
                    }
            }else{
                $this->_messageManager->addNotice('Order for this order Id #'.$missingOrderId.' is already created!'); 
            }    
        } //foreach    

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('ordercreate/missingorder/index');
        return $resultRedirect;  

    } 
}
