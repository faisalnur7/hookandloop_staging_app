<?php
/**
 * Ravedigital_Ordercreate
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2016 Avalara, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Ravedigital\Ordercreate\Observer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;

/**
 * Class PreventDuplicateOrderObserver
 */
class PreventDuplicateOrderObserver implements ObserverInterface
{
    /**
     * @var ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var TransactionCollection
     */
    protected $_transactionCollection;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        TransactionCollectionFactory $_transactionCollection,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_transactionCollection = $_transactionCollection;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->resourceConnection = $resourceConnection;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws LocalizedException
     *  - Get quote Id from currently processing order.
     *  - Fetch order by the quote Id using filter duration of maximum 10 mins.
     *  - If order found then checked if transaction is exist for the last order then discard the currently processing order.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $currentOrderIncrementId = $order->getIncrementId();
        $connection = $this->resourceConnection->getConnection();
        $salesOrderTable = $this->resourceConnection->getTableName('sales_order');
        $salesOrderPaymentTable = $this->resourceConnection->getTableName('sales_order_payment');
        $selectedTime = date('Y-m-d h:i:s');
        $endTime = strtotime("-10 minutes", strtotime($selectedTime));
        $last10min = date('Y-m-d h:i:s', $endTime);
        $sql = "SELECT so.entity_id, so.increment_id, so.customer_email, so.quote_id, sop.method, so.created_at FROM `".$salesOrderTable."` so INNER JOIN `".$salesOrderPaymentTable."` sop ON so.entity_id = sop.parent_id WHERE so.quote_id = ".$quoteId." AND so.created_at >= '$last10min' ORDER BY so.entity_id DESC";
       
        if($result = $connection->fetchRow($sql)) {
            if ($result['method'] == 'paypal_express' && $result['customer_email'] == $order->getCustomerEmail()) {
                $transactions = $this->_transactionCollection->create()->addFieldToFilter('order_id', $result['entity_id']);
                if ($transactions->count()) {
                    foreach ($transactions as $transaction) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $logger = $objectManager->create('\Psr\Log\LoggerInterface');
                        $message = "Order has already been placed for the quote. Duplicate Order ID: ".$currentOrderIncrementId;
                        //$message = "Order has already been placed for the quote. Original Order ID: ".$result['increment_id'];
                        $logger->info($message);
                        
                        /*$this->messageManager->addNotice($message);
                        $cartUrl = $this->_url->getUrl('checkout/cart/index');
                        $this->_responseFactory->create()->setRedirect($cartUrl)->sendResponse();
                        exit;*/
                    }
                }
            }
        }
    }
}
