<?php
namespace Ravedigital\Custom\Helper;

class Customhelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_cart;
    protected $_checkoutSession;
    protected $_productloader;
    protected $_objectManager;
    protected $_scopeConfigInterface;
    protected $_storeManager;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_productloader = $_productloader;
        $this->_objectManager = $objectmanager;
         $this->_storeManager = $storeManager;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        
        parent::__construct($context);
    }
    
    public function getCart()
    {
        return $this->_cart;
    }
    
    public function getCheckoutSession()
    {
        return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }
   public function getShippingMethod()
    {
        return $this->getCart()->getQuote()->getShippingAddress()->getShippingMethod();
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    public function getLoadProductBySku($sku)
    {
        $product = $this->_objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface')->get($sku);
        ;
        return $product;
    }

    public function getCartQty()
    {
      
        $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');
        $itemsVisible=$cart->getQuote()->getAllVisibleItems();

        $totalItems = $cart->getQuote()->getItemsCount();
        $totalQuantity = $cart->getQuote()->getItemsQty();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/wishlisttocart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
     
        $logger->info('totalItems:');
        $logger->info($totalItems);
        $logger->info('totalQuantity:');
        $logger->info($totalQuantity);


        $totalqty = 3;
        foreach ($itemsVisible as $item) {
            if ($item->getProductType() == 'configurable') {
                $logger->info('IFFF:');
               // $logger->info($item);
                $options = $item->getOptionByCode('simple_product')->getData();
                $productId = $options["product_id"];
                $child = $this->getLoadProduct($productId);
                //$child = $product->load($productId);

                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            } else {
                $logger->info('ELSE:');
                $productId = $item->getProductId();
                $child = $this->getLoadProduct($productId);
                //$child = $product->load($productId);
                $measurement = $child->getMeasurementSoldInSize();
                $qty = $item->getQty();
                $computed_qty = $qty / $measurement;
                $totalqty += $computed_qty;
            }
        }
        return $totalqty;
    }

    public function getConfig($path = '')
    {
        if ($path) {
            return $this->_scopeConfigInterface->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig;
    }

    public function getStockItem($productId)
    {
        return $this->_objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($productId);
    }

    public function getStore()
    {
        return $this->_storeManager;
    }
}
