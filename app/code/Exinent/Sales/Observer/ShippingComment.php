<?php
namespace Exinent\Sales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ShippingComment implements ObserverInterface
{
	private $logger;
	protected $messageManager;

	public function __construct(
		\Psr\Log\LoggerInterface $logger,\Magento\Framework\View\Element\Context $context) {
		$this->logger = $logger;
		$this->_layout = $context->getLayout();
	}
	public function execute(EventObserver $observer)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $observer->getEvent()->getOrder();
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shippingcomment.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		foreach($order->getAllItems() as $item)
		{
			$Stockrepository = $objectManager->create('Magento\CatalogInventory\Model\Stock\StockItemRepository');
			$proQty = $objectManager->create('Magento\CatalogInventory\Api\StockStateInterface');
			$orgQty = $proQty->getStockQty($item->getProductId(), $item->getStore()->getWebsiteId());
			$productStock = $Stockrepository->get($item->getProductId());
	    //$proName[] = $item->getName(); // product name
			$product_Stock = $productStock->getIsInStock();
			$org_qty = $orgQty;
			$qty = $item->getQtyOrdered();
			if($product_Stock != true || $qty > $org_qty){
				$ProdustIds[]= $item->getProductId();
			}
		}
	}
}