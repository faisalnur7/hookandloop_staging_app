<?php
namespace Ravedigital\M2ePro\Model\Magento\Quote;
use Ess\M2ePro\Helper\Data\GlobalData;
use Ess\M2ePro\Helper\Factory;
use Ess\M2ePro\Model\AbstractModel;
use Ess\M2ePro\Model\Currency;
use Ess\M2ePro\Model\Ebay\Order;
use Ess\M2ePro\Model\Exception;
use Ess\M2ePro\Model\Magento\Quote\Manager;
use Ess\M2ePro\Model\Magento\Quote\Store\Configurator;
use Ess\M2ePro\Model\Order\Item\ProxyObject;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Group;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\OrderIncrementIdChecker;
use Magento\Tax\Model\Calculation;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Builds the quote object, which then can be converted to magento order
 */
class Builder extends AbstractModel
{
    const PROCESS_QUOTE_ID = 'PROCESS_QUOTE_ID';
    //########################################
    protected $proxyOrder;

    /** @var  Quote */
    protected $quote;
    protected $currency;
    protected $magentoCurrencyFactory;
    protected $calculation;
    protected $storeConfig;
    protected $productResource;

    /** @var Manager */
    protected $quoteManager;

    /** @var Configurator */
    protected $storeConfigurator;

    /** @var OrderIncrementIdChecker */
    protected $orderIncrementIdChecker;

    protected $amazonOrders;

    protected $walmartOrders;

    protected $ebayOrders;

    protected $productCollection;

    //########################################

    public function __construct(
        \Ess\M2ePro\Model\Order\ProxyObject $proxyOrder,
        Currency $currency,
        CurrencyFactory $magentoCurrencyFactory,
        \Ess\M2ePro\Model\Factory $modelFactory,
        Calculation $calculation,
        ReinitableConfigInterface $storeConfig,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        Factory $helperFactory,
        Manager $quoteManager,
        OrderIncrementIdChecker $orderIncrementIdChecker,
        \Ess\M2ePro\Model\Amazon\Order $amazonOrders,
        \Ess\M2ePro\Model\Walmart\Order $walmartOrders,
        Order $ebayOrders,
        Product $productCollection
    ) {
        $this->proxyOrder = $proxyOrder;
        $this->currency = $currency;
        $this->magentoCurrencyFactory = $magentoCurrencyFactory;
        $this->calculation = $calculation;
        $this->storeConfig = $storeConfig;
        $this->productResource = $productResource;
        $this->quoteManager = $quoteManager;
        $this->orderIncrementIdChecker = $orderIncrementIdChecker;
        $this->amazonOrders = $amazonOrders;
        $this->walmartOrders = $walmartOrders;
        $this->ebayOrders = $ebayOrders;
        $this->productCollection = $productCollection;
        parent::__construct($helperFactory, $modelFactory);
    }

    public function __destruct()
    {
        if ($this->storeConfigurator === null) {
            return;
        }

        $this->storeConfigurator->restoreOriginalStoreConfigForOrder();
    }

    //########################################

    public function build()
    {
        try {
            // do not change invoke order
            // ---------------------------------------
            $this->initializeQuote();
            $this->initializeCustomer();
            $this->initializeAddresses();
            $this->configureStore();
           // $this->configureTaxCalculation();
            $this->initializeCurrency();
            $this->initializeShippingMethodData();
            $this->initializeQuoteItems();
            $this->initializePaymentMethodData();

            //$this->adjustOrderTotal();

            $this->quote = $this->quoteManager->save($this->quote);

            $this->prepareOrderNumber();
            return $this->quote;
            // ---------------------------------------
        } catch (\Exception $e) {

            // Remove ordered items from customer cart
            $this->quote->setIsActive(false);
            $this->quote->removeAllAddresses();
            $this->quote->removeAllItems();

            $this->quote->save();
            throw $e;
        }
    }

    //########################################

    private function initializeQuote()
    {
        $this->quote = $this->quoteManager->getBlankQuote();

        $this->quote->setCheckoutMethod($this->proxyOrder->getCheckoutMethod());
        $this->quote->setStore($this->proxyOrder->getStore());
        $this->quote->getStore()->setData('current_currency', $this->quote->getStore()->getBaseCurrency());

        /**
         * The quote is empty at this moment, so it is not need to collect totals
         */
        $this->quote->setTotalsCollectedFlag(true);
        $this->quote = $this->quoteManager->save($this->quote);
        $this->quote->setTotalsCollectedFlag(false);

        $this->quote->setIsM2eProQuote(true);
        $this->quote->setIsNeedToSendEmail($this->proxyOrder->isMagentoOrdersCustomerNewNotifyWhenOrderCreated());
        /*$this->quote->setNeedProcessChannelTaxes(
            $this->proxyOrder->isTaxModeChannel() ||
            ($this->proxyOrder->isTaxModeMixed() &&
                ($this->proxyOrder->hasTax() || $this->proxyOrder->getWasteRecyclingFee()))
        );*/

        $this->quoteManager->replaceCheckoutQuote($this->quote);

        /** @var GlobalData $globalDataHelper */
        $globalDataHelper = $this->getHelper('Data\GlobalData');

        $globalDataHelper->unsetValue(self::PROCESS_QUOTE_ID);
        $globalDataHelper->setValue(self::PROCESS_QUOTE_ID, $this->quote->getId());
    }

    //########################################

    private function initializeCustomer()
    {
        if ($this->proxyOrder->isCheckoutMethodGuest()) {
            $this->quote
                ->setCustomerId(null)
                ->setCustomerEmail($this->proxyOrder->getBuyerEmail())
                ->setCustomerFirstname($this->proxyOrder->getCustomerFirstName())
                ->setCustomerLastname($this->proxyOrder->getCustomerLastName())
                ->setCustomerIsGuest(true)
                ->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);

            return;
        }

        $this->quote->assignCustomer($this->proxyOrder->getCustomer());
    }

    //########################################

    private function initializeAddresses()
    {
        $billingAddress = $this->quote->getBillingAddress();
        $billingAddress->addData($this->proxyOrder->getBillingAddressData());

        $billingAddress->setLimitCarrier('m2eproshipping');
        $billingAddress->setShippingMethod('m2eproshipping_m2eproshipping');
        $billingAddress->setCollectShippingRates(true);
        $billingAddress->setShouldIgnoreValidation($this->proxyOrder->shouldIgnoreBillingAddressValidation());

        // ---------------------------------------

        $shippingAddress = $this->quote->getShippingAddress();
        $shippingAddress->setSameAsBilling(0); // maybe just set same as billing?
        $shippingAddress->addData($this->proxyOrder->getAddressData());

        $shippingAddress->setLimitCarrier('m2eproshipping');
        $shippingAddress->setShippingMethod('m2eproshipping_m2eproshipping');
        $shippingAddress->setCollectShippingRates(true);

        // ---------------------------------------
    }

    //########################################

    /**
     * Configure store (invoked only after address, customer and store initialization and before price calculations)
     */
    private function configureStore()
    {
        $this->storeConfigurator = $this->modelFactory->getObject(
            'Magento_Quote_Store_Configurator',
            ['quote' => $this->quote, 'proxyOrder' => $this->proxyOrder]
        );

        $this->storeConfigurator->prepareStoreConfigForOrder();
    }

    //########################################

    private function initializeCurrency()
    {
        /** @var $currencyHelper Currency */
        $currencyHelper = $this->currency;

        if ($currencyHelper->isConvertible($this->proxyOrder->getCurrency(), $this->quote->getStore())) {
            $currentCurrency = $this->magentoCurrencyFactory->create()->load(
                $this->proxyOrder->getCurrency()
            );
        } else {
            $currentCurrency = $this->quote->getStore()->getBaseCurrency();
        }

        $this->quote->getStore()->setData('current_currency', $currentCurrency);
    }

    //########################################

    private function initializeShippingMethodData()
    {
        $order = $this->getOrder();
        $shipping_data = $this->proxyOrder->getShippingData();
        $this->getHelper('Data\GlobalData')->unsetValue('shipping_data');
        if ($order['component_mode'] === 'amazon') {
            $amazon_shipping_price = $order['shipping_price'];
            $shipping_data['shipping_price'] = $amazon_shipping_price;
        } else if ($order['component_mode'] === 'walmart') {
            $shipping_data['shipping_price'] = $shipping_data['shipping_price'];
        } else {
            $shipping_data['shipping_price'] = $shipping_data['shipping_price'];
        }
        $this->getHelper('Data\GlobalData')->setValue('shipping_data', $shipping_data);

        $this->proxyOrder->initializeShippingMethodDataPretendedToBeSimple();
    }

    //########################################

    private function getOrder()
    {
        $order_id = $this->proxyOrder->getPaymentData()['channel_order_id'];
        $order_details = $this->proxyOrder->getPaymentData();
        if ($order_details['component_mode'] === 'amazon') {
            $order = $this->amazonOrders->getCollection()
                ->addFieldToFilter('amazon_order_id', $order_id);
        } else if ($order_details['component_mode'] === 'walmart') {
            $order = $this->walmartOrders->getCollection()
                ->addFieldToFilter('walmart_order_id', $order_id);
        }
        $order = $order->getData()[0];
        $order['component_mode'] = $order_details['component_mode'];
        return $order;
    }

    /**
     * @throws Exception
     */
    protected function initializeQuoteItems()
    {
        $writer = new Stream(BP . '/var/log/m2epro_orders.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info('//////////// Start Rave Builder.php /////////////////');
        $logger->info('Start New order process from here');

        foreach ($this->proxyOrder->getItems() as $item) {
            $this->clearQuoteItemsCache();

            /** @var \Ess\M2ePro\Model\Magento\Quote\Item $quoteItemBuilder */
            $quoteItemBuilder = $this->modelFactory->getObject('Magento_Quote_Item', [
                'quote' => $this->quote,
                'proxyItem' => $item
            ]);

            $product = $quoteItemBuilder->getProduct();

            $logger->info('Product sku line 339- ' . $product->getSku());
            if (!$item->pretendedToBeSimple()) {
                $simpleConfig = $item->getAssociatedProducts();
                /*if(is_array($simpleConfig)){
                    foreach ($simpleConfig as $key => $value) {
                        //if($item->getProductId() != $value){
                            $product = $this->productCollection->load($value);
                            $logger->info('Product sku line 346- '.$product->getSku()); 
                        //}    
                    }
                }*/
                $logger->info('Item predend to simple - if');
                $this->initializeQuoteItem($item, $quoteItemBuilder, $product, $quoteItemBuilder->getRequest());
                continue;
            }
            // ---------------------------------------

            $totalPrice = 0;
            $products = [];
            foreach ($product->getTypeInstance()->getAssociatedProducts($product) as $associatedProduct) {
                /** @var Product $associatedProduct */
                if ($associatedProduct->getQty() <= 0) { // skip product if default qty zero
                    continue;
                }

                $totalPrice += $associatedProduct->getPrice();

                $products[] = $associatedProduct;
            }
            $logger->info('total price - ' . $totalPrice);

            // ---------------------------------------
            $logger->info('associatedProduct');
            foreach ($products as $associatedProduct) {
                $logger->info('associatedProduct sku - ' . $associatedProduct->getSku());
                $item->setQty($associatedProduct->getQty() * $item->getOriginalQty());

                $productPriceInSetPercent = ($associatedProduct->getPrice() / $totalPrice) * 100;
                $logger->info('associatedProduct productPriceInSetPercent - ' . $productPriceInSetPercent);
                $productPriceInItem = (($item->getOriginalPrice() * $productPriceInSetPercent) / 100);
                $logger->info('associatedProduct productPriceInItem - ' . $productPriceInItem);
                //$item->setPrice($productPriceInItem / $associatedProduct->getQty());
                $item->setPrice($productPriceInItem / $associatedProduct->getQty());
                $logger->info('associatedProduct product price - ' . $productPriceInItem / $associatedProduct->getQty());
                // $associatedProduct->setTaxClassId($product->getTaxClassId());

                /** @var \Ess\M2ePro\Model\Magento\Quote\Item $quoteItemBuilder */
                $quoteItemBuilder = $this->modelFactory->getObject('Magento_Quote_Item', [
                    'quote' => $this->quote,
                    'proxyItem' => $item
                ]);

                $this->initializeQuoteItem(
                    $item,
                    $quoteItemBuilder,
                    $associatedProduct,
                    $quoteItemBuilder->getRequest()
                );
            }
        }

        $allItems = $this->quote->getAllItems();
        $this->quote->getItemsCollection()->removeAllItems();

        foreach ($allItems as $item) {
            $item->save();
            $this->quote->getItemsCollection()->addItem($item);
        }
        $logger->info('//////////// Stop Rave Builder.php /////////////////');
    }

    /**
     * Mage_Sales_Model_Quote_Address caches items after each collectTotals call. Some extensions calls collectTotals
     * after adding new item to quote in observers. So we need clear this cache before adding new item to quote.
     */
    private function clearQuoteItemsCache()
    {
        foreach ($this->quote->getAllAddresses() as $address) {
            $address->unsetData('cached_items_all');
            $address->unsetData('cached_items_nominal');
            $address->unsetData('cached_items_nonnominal');
        }
    }

    //########################################

    /**
     * @param ProxyObject $item
     * @param \Ess\M2ePro\Model\Magento\Quote\Item $quoteItemBuilder
     * @param Product $product
     * @param DataObject $request
     * @throws Exception
     */
    protected function initializeQuoteItem($item, $quoteItemBuilder, $product, $request)
    {   $m2epro_order = $this->getOrder();
        $writer = new Stream(BP . '/var/log/m2epro_orders.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info('Start New order process from here');

        $simpleConfig = $item->getAssociatedProducts();
        if (is_array($simpleConfig)) {
            foreach ($simpleConfig as $key => $value) {
                $logger->info('value - ' . $value);
                $simpleproduct = $this->productCollection->load($value);
            }
        }
        if ($product->getTypeId() === 'configurable') {
            $soldInSize = $simpleproduct->getData('measurement_sold_in_size');
            $totalQty = $item->getQty() * $soldInSize;
            $logger->info('totalQty - '.$totalQty);
            $request->setQty($totalQty);
            $productOriginalPrice = $simpleproduct->getPrice();
            if($m2epro_order['component_mode'] === 'amazon'){
                $m2eproPrice = $simpleproduct->getAmazonPrice();
            } else if($m2epro_order['component_mode'] === 'walmart'){
                $m2eproPrice = $simpleproduct->getWalmartPrice();
            } else {
                $m2eproPrice = $simpleproduct->getEbayPrice();
            }
        } else {
            $soldInSize = $product->getData('measurement_sold_in_size');
            $productOriginalPrice = $product->getPrice();

            if($m2epro_order['component_mode'] === 'amazon'){
                $m2eproPrice = $product->getAmazonPrice();
            } else if($m2epro_order['component_mode'] === 'walmart'){
                $m2eproPrice = $product->getWalmartPrice();
            } else {
                $m2eproPrice = $product->getEbayPrice();
            }
        }
        $logger->info('line 258 SKU: ' . $simpleproduct->getSku());
        $logger->info('line 259 SKU: ' . $product->getSku());
        if ((!$soldInSize && !is_numeric($soldInSize)) || $soldInSize == false) {
            $soldInSize = 1;
        }

        $logger->info('Sold size: ' . $soldInSize);
        $logger->info('SKU: ' . $product->getSku());
        $logger->info('ProductOriginalPrice: ' . $productOriginalPrice);
        $logger->info('Productm2eproPrice: ' . $m2eproPrice);

        // see Mage_Sales_Model_Observer::substractQtyFromQuotes
        $this->quote->setItemsCount($this->quote->getItemsCount() + 1);
        $this->quote->setItemsQty((float)$this->quote->getItemsQty() + $request->getQty());


        $result = $this->quote->addProduct($product, $request);
        if (is_string($result)) {
            throw new Exception($result);
        }

        $quoteItem = $this->quote->getItemByProduct($product);
        if ($quoteItem === false) {
            return;
        }
        $quoteItem->setStoreId($this->quote->getStoreId());

        if ($soldInSize) {
            $quoteItem->setOriginalCustomPrice($m2eproPrice / $soldInSize);
        } else {
            $quoteItem->setOriginalCustomPrice($m2eproPrice);
        }
        $quoteItem->setOriginalPrice($productOriginalPrice);
        $quoteItem->setBaseOriginalPrice($productOriginalPrice);

        $quoteItem->setNoDiscount(1);
        $logger->info('quote getItemsQty: ' . $this->quote->getItemsQty());
        $logger->info('quote getItemsCount: ' . $this->quote->getItemsCount());
        $logger->info('request getQty: ' . $request->getQty());
        $logger->info('origionalprice * soldInSize: ' . $m2eproPrice * $soldInSize);

        $logger->info('Logging end');
        /* foreach ($quoteItem->getChildren() as $itemChildren) {
             $itemChildren->getProduct()->setTaxClassId($quoteItem->getProduct()->getTaxClassId());
         }*/

        $giftMessageId = $quoteItemBuilder->getGiftMessageId();
        if (!empty($giftMessageId)) {
            $quoteItem->setGiftMessageId($giftMessageId);
        }
        $quoteItem->setAdditionalData($quoteItemBuilder->getAdditionalData($quoteItem));

        $quoteItem->setWasteRecyclingFee($item->getWasteRecyclingFee() / $item->getQty());

    }

    //########################################

    private function initializePaymentMethodData()
    {
        $quotePayment = $this->quote->getPayment();
        $quotePayment->importData($this->proxyOrder->getPaymentData());
    }

    //########################################

    private function prepareOrderNumber()
    {
        if ($this->proxyOrder->isOrderNumberPrefixSourceChannel()) {
            $orderNumber = $this->proxyOrder->getOrderNumberPrefix() . $this->proxyOrder->getChannelOrderNumber();
            $this->orderIncrementIdChecker->isIncrementIdUsed($orderNumber) && $orderNumber .= '(1)';

            $this->quote->setReservedOrderId($orderNumber);
            return;
        }

        $orderNumber = $this->quote->getReservedOrderId();
        empty($orderNumber) && $orderNumber = $this->quote->getResource()->getReservedOrderId($this->quote);
        $orderNumber = $this->proxyOrder->getOrderNumberPrefix() . $orderNumber;

        if ($this->orderIncrementIdChecker->isIncrementIdUsed($orderNumber)) {
            $orderNumber = $this->quote->getResource()->getReservedOrderId($this->quote);
        }

        $this->quote->setReservedOrderId($orderNumber);
       // $this->sendOrderEmailToAdmin($orderNumber);
    }

   

    private function configureTaxCalculation()
    {
        // this prevents customer session initialization (which affects cookies)
        // see Mage_Tax_Model_Calculation::getCustomer()
        $this->calculation->setCustomer($this->quote->getCustomer());
    }

    private function adjustOrderTotal()
    {
        $shipping_address = $this->quote->getShippingAddress();
        $amazonOrders = $this->getAmazonOrder();
        if ($amazonOrders) {
            $difference = $amazonOrders->getData()[0]['paid_amount'] - $shipping_address->getGrandTotal();
        }
        $total = $this->quote->getSubtotal() + $difference;
        $shipping_address->setSubtotal($total);
        $shipping_address->setBaseSubtotal($total);
        $shipping_address->setSubtotalWithDiscount($total);
        $shipping_address->setBaseSubtotalWithDiscount($total);
        $shipping_address->setSubtotalInclTax($total);
        $shipping_address->setBaseSubtotalIncTax($total);
        $shipping_address->setGrandTotal($shipping_address->getGrandTotal() + $difference);
        $shipping_address->setBaseGrandTotal($shipping_address->getBaseGrandTotal() + $difference);
        $this->quote->setCutToLengthCharges($difference);
        $this->quote->setHandlingCharges($difference);
        $this->quote->setSubtotal($total);
        $this->quote->setBaseSubtotal($total);
        $this->quote->setSubtotalWithDiscount($total);
        $this->quote->setBaseSubtotalWithDiscount($total);
        $this->quote->setGrandTotal($total);
        $this->quote->setBaseGrandTotal($total);
        $this->quote->setSubtotal($total);
        $this->quote->setBaseSubtotal($total);
    }

    //########################################
}
