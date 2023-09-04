<?php

/*
 * @category   AmazonProductMapping
 * @author     pawan 
 * @copyright  Exinent_AmazonProductMapping
 * 
 */

namespace Exinent\AmazonProductMapping\Model\Cron;

class Amazon {

    const XML_NOTIFY_EMAIL = 'dev/debug/amazon_order_alert_enable';
    const XML_NOTIFY_VALUE = 'dev/debug/amazon_order_alert_emails';

    protected $_logger;
    protected $amazonFactory;
    protected $_transportBuilder;
    protected $_request;
    protected $_storeManager;
    protected $scopeConfig;
    protected $_amazonhelper;

    public function __construct(
    \Psr\Log\LoggerInterface $logger,\Ess\M2ePro\Helper\Component\Amazon $amazonhelper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Request\Http $request, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Amazon\Factory $amazonFactory, \Ess\M2ePro\Helper\Factory $helperFactory, \Ess\M2ePro\Model\ActiveRecord\Factory $activeRecordFactory, \Ess\M2ePro\Model\Factory $modelFactory, \Magento\Framework\Message\Manager $messageManager, \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_logger = $logger;
        $this->_amazonhelper = $amazonhelper;
        $this->timezone = $timezone;
        $this->amazonFactory = $amazonFactory;
        $this->_request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
      $this->activeRecordFactory=$activeRecordFactory;
        $this->messageManager = $messageManager;
        $this->productFactory = $productFactory;
//        parent::__construct($helperFactory, $activeRecordFactory, $modelFactory);
    }

    /**
     * Write to  It is used for mapping Amazon product with Magento product for order creation
     * @return void
     */
    public function productMapping() {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product_mapping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $date = $this->timezone->date();
        $date = $date->format('Y-m-d H:i:s'); //25/10/18 09:15:07 AM [] []
        //2018-10-25 09:21:09 AM1970-01-01 00:00:001970-01-01 23:59:59 [] []
        $dateStart = date('Y-m-d' . ' 00:00:00', strtotime(str_replace('-', '/', $date)));
        $dateEnd = date('Y-m-d' . ' 23:59:59', strtotime(str_replace('-', '/', $date)));
        /** @var $orders \Ess\M2ePro\Model\Order[] */
        $collection = $this->activeRecordFactory->getObject('Order')->getCollection(); //$this->activeRecordFactory->getObject('Order')
        $collection->addFieldToSelect('id');
        $collection->addFieldToFilter('magento_order_id', array('null' => true));
        $collection->addFieldToFilter('create_date', [
            'from' => $dateStart,
            'to' => $dateEnd,
            'date' => true
        ]);
        $product = $this->productFactory->create();
        $orderIds = $collection->getData();
        if (count($orderIds) > 0) {
            foreach ($orderIds as $orderId) {
                /** @var $orderItem \Ess\M2ePro\Model\Order\Item */
                $orderItemCollection = $this->activeRecordFactory->getObject('Order\Item')->getCollection()->addFieldToFilter('order_id', [
                            'equal' => $orderId
                        ])->addFieldToSelect('id')->addFieldToSelect('product_id')->addFieldToSelect('product_details');
                $orderItemIds = $orderItemCollection->getData('id');
                foreach ($orderItemIds as $orderItemId) {

                    $simpleProductCollection = $this->amazonFactory->getObject('Order\Item')->getCollection()->addFieldToFilter('order_item_id', [
                                'equal' => $orderItemId['id']
                            ]);
              
                    $simpleProductSkus = $simpleProductCollection->getData();
                   try {
                        foreach ($simpleProductSkus as $simpleProductSku) {
                            
                            if ($simpleProductSku['sku'] == "amn-195872-3610") {
                                $simpleProductSku = '195872-3610';
                            } else {
                                $find = array(
                                    "az-",
                                    "am-",
                                    "-azn",
                                    "amz-",
                                    "-amz-azn",
                                    "-az",
                                    "DURAGRIP-",
                                    "amn-",
                                    "_2472",
                                    "-amz",
                                    "-3610",
                                    "sim-",
                                    "VEL-",
                                    "-dis",
                                    "DG-",
                                    "DURAGRIP_"
                                );
                                $replace = array(
                                    ""
                                );

                                $arr = array(
                                    $simpleProductSku['sku']
                                );

                                $sku = str_replace($find, $replace, $arr);

                                $arr = explode("_", $sku[0]);
                                $simpleProductSku = $arr[0];
                            }
                            $simpleProductId = $product->getIdBySku($simpleProductSku);
                            if (!empty($simpleProductId)) {
                                $orderItemId['product_id'];
                                $id = $orderItemId['id'];
                                $data = [
                                    "product_id" => $simpleProductId
                                ];
                               
                                $orderItemModel = $this->activeRecordFactory->getObject('Order\Item')->load($id)->addData($data);
                                try {
                                    $orderItemModel->setId($id)->save();
//                                    $logger->info("Amazon OrderId: " . $orderId['id'] . " Data updated here is simple productId: " . $simpleProductId);
                                } catch (Exception $ex) {
//                                    $logger->info($e->getMessage());
                                }
                            } else {
//                                $logger->info("Amazon Order id " . $orderId['id'] . " and Product SKU " . $simpleProductSku . " not found");
                            }
                        }
                    } catch (Exception $ex) {
//                        $logger->info($e->getMessage());
                    }
                }
            }
        } else {
//            $logger->info("No order for mapping is available ");
        }
    }

    public function orderCreation() {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Amazon_order_creation.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$logger->info('test');
        $date = $this->timezone->date();
        $date = $date->format('Y-m-d H:i:s');
        $dateStart = date('Y-m-d' . ' 00:00:00', strtotime(str_replace('-', '/', $date)));
        $dateEnd = date('Y-m-d' . ' 23:59:59', strtotime(str_replace('-', '/', $date)));
//        $orderLogs = $this->activeRecordFactory->getObject('Order\Log')->getCollection(); //$this->activeRecordFactory->getObject('Order')
        $orderLogds = $this->activeRecordFactory->getObject('Order')->getCollection();
        $orderLogds->addFieldToFilter('create_date', [
            'from' => $dateStart,
            'to' => $dateEnd,
            'date' => true
        ]);
        $orderLogds->addFieldToFilter('magento_order_id', array('null' => true));
//        $orderLogs->addFieldToFilter('type', [
//            'eq' => 4
//        ]);
//        $orderLogs->addFieldToSelect('*');
//        $orderLogs->setOrder('order_id', 'ASC');
//        $orderLogs->addFieldToFilter('create_date', [
//            'from' => $dateStart,
//            'to' => $dateEnd,
//            'date' => true
//        ]);
        $orderLogsCount = $orderLogds->getData();
        if (count($orderLogsCount) > 0) {
            $checkOrderIds = array();
            foreach ($orderLogds as $orderLog) {
//                $description = $orderLog->getData('description');
//                 print_r($description); 
//                if (preg_match('/Magento Order was not created/', $description)) {
                    array_push($checkOrderIds, $orderLog->getData('id'));
//                }
            }
//            print_r($checkOrderIds); 
//            print_r($orderLog->getData());
            $orderIds = array();
            $myArray = $checkOrderIds;
            $uniqueOrderIds = array_unique($checkOrderIds);
//            $my_array_values = array_count_values($myArray);
//
//            while (list ($key, $val) = each($my_array_values)) {
//                // array_push($uniqueOrderIds, $key);       
//                if (!($val >= 2)) {
//                    array_push($uniqueOrderIds, $key);
//                }
//                // else{
//                //   echo "invalid";
//                // }
//            }
//print_r(array_unique($checkOrderIds)); 
            $orderLogIds = $uniqueOrderIds;
//            print_r($orderLogIds);
            if (count($orderLogIds) > 0) {
                foreach ($orderLogIds as $orderLogId) {
                    $orderCollections = $this->activeRecordFactory->getObject('Order')->getCollection(); //$this->activeRecordFactory->getObject('Order')
                    $orderCollections->addFieldToFilter('id', [
                        'equal' => $orderLogId
                    ]);
                    $orderCollections->addFieldToFilter('magento_order_id', array('null' => true));
                    $orderCollections->addFieldToSelect('id');
                    $orderCollections->addFieldToSelect('store_id');
                    $orderIds = $orderCollections->getData();
                    if (count($orderIds) == '1') {
                        foreach ($orderIds as $orderId) {
                            try {
                                //$order = Mage::helper('M2ePro/Component_Amazon')->getObject('Order', (int) $orderId['id']);
//                                $order=$this->_amazonhelper->getObject('Order', (int) $orderId['id']); //Need to check helper
                                $order=$this->amazonFactory->getObjectLoaded('Order', (int)$orderId['id']);
                                $order->createMagentoOrder();
                                $order->createInvoice();
                                $order->updateMagentoOrderStatus();
//                                $logger->info("order created order id: " . $orderId['id']);
                            } catch (Exception $e) {
                                $amazonOrderCollections = $this->activeRecordFactory->getObject('Amazon/Order')->getCollection(); //$this->activeRecordFactory->getObject('Order')
                                $amazonOrderCollections->addFieldToFilter('order_id', [
                                    'equal' => $orderId['id']
                                ]);
                                $amazonOrderCollections->addFieldToSelect('amazon_order_id');
                                $amazonOrders = $amazonOrderCollections->getData();
                                foreach ($amazonOrders as $amazonOrder) {
                                    $amazonOrderId = $amazonOrder['amazon_order_id'];
                                }
                                $errorMsg = $e->getMessage();
                                $orderId = $orderId['id'];
                                $orderItemCollection = $this->activeRecordFactory->getObject('Order/Item')->getCollection();
                                $orderItemCollection->addFieldToFilter('order_id', [
                                    'equal' => $orderId
                                ]);
                                $orderItemCollection->addFieldToSelect('id');
                                $orderItemCollection->addFieldToSelect('product_id');
                                foreach ($orderItemCollection as $orderItem) {
                                    $productId = $orderItem->getData('product_id');
                                    $orderId['store_id'];
                                }
//                                $logger->info("Amazon Order: " . $amazonOrderId . " " . $errorMsg . " " . $productId);
                                $configValue = $this->scopeConfig->getValue(self::XML_NOTIFY_EMAIL, $orderId['store_id']);
                                $configEmailList = $this->scopeConfig->getValue(self::XML_NOTIFY_VALUE, $orderId['store_id']);
                                if ($configValue && !empty($configEmailList)) {
                                    $this->_sendAmazonOrderMail($amazonOrderId, $errorMsg, $productId, $configEmailList);
                                } else {
//                                    $logger->info("Check system configuration setting");
                                }
                            }
                        }
                    }
                }
            } else {
//                $logger->info("No unique order exist.");
            }
        } else {
//            $logger->info("No error log exist.");
        }
    }

    private function _sendAmazonOrderMail($amazonOrderId, $errorMsg, $productId, $configEmailList) {
        $store = $this->_storeManager->getStore()->getId();
        $emailTemplateVariables = [
            'amazon_order_id' => $amazonOrderId,
            'product_id' => $productId,
            'error_msg' => $errorMsg
        ];
        $emails = explode(',', $configEmailList); //Need to check this example $emails = ['email1@test.com', 'email2@test.com'];
        $emailTemplate = $this->_transportBuilder->setTemplateIdentifier('1');
        $emailTemplate->setTemplateOptions(['area' => 'frontend', 'store' => $store]);
        $emailTemplate->setTemplateVars($emailTemplateVariables);
        $emailTemplate->setFrom(['name' => 'Hookandloop', 'email' => 'info@hookandloop.com']);
        $emailTemplate->addTo($emails);
        $emailTemplate->getTransport();
        $emailTemplate->sendMessage();
    }

}
