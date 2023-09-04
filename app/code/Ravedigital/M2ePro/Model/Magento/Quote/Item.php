<?php

namespace Ravedigital\M2ePro\Model\Magento\Quote;

use Ess\M2ePro\Model\Exception;
use Ess\M2ePro\Model\Factory;
use Ess\M2ePro\Model\Magento\Tax\Helper;
use Ess\M2ePro\Model\Order\Item\Proxy;
use Ess\M2ePro\Model\Order\Item\ProxyObject;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObject;
use Magento\GiftMessage\Model\Message;
use Magento\GiftMessage\Model\MessageFactory;
use Magento\Quote\Model\Quote;
use Magento\Tax\Model\Calculation;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class Item extends \Ess\M2ePro\Model\Magento\Quote\Item
{
    protected $taxHelper;

    protected $productFactory;

    protected $calculation;

    protected $messageFactory;

    /** @var Quote */
    protected $quote;

    /** @var Proxy */
    protected $proxyItem;

    /** @var Product */
    protected $product;

    /** @var Message */
    protected $giftMessage;

    protected $_productTypeConfigurable;
    protected $_productCatalog;

    //########################################

    public function __construct(
        \Ess\M2ePro\Helper\Factory $helperFactory,
        Helper $taxHelper,
        ProductFactory $productFactory,
        Calculation $calculation,
        MessageFactory $messageFactory,
        Quote $quote,
        ProxyObject $proxyItem,
        Factory $modelFactory,
        Configurable $productTypeConfigurable,
        Product $_productCatalog
    ) {
        $this->taxHelper = $taxHelper;
        $this->productFactory = $productFactory;
        $this->calculation = $calculation;
        $this->messageFactory = $messageFactory;
        $this->quote = $quote;
        $this->proxyItem = $proxyItem;
        $this->_productTypeConfigurable = $productTypeConfigurable;
        $this->_productCatalog = $_productCatalog;
        parent::__construct($taxHelper, $productFactory, $calculation, $messageFactory, $quote, $proxyItem, $helperFactory, $modelFactory);
    }

    //########################################

    public function getRequest()
    {
        $writer = new Stream(BP . '/var/log/m2epro_orders.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info('//////////// Start Rave Item.php /////////////////');

        $request = new DataObject();

        if ($this->proxyItem->getMagentoProduct()->isConfigurableType() == true) {
            $soldInSize = $this->proxyItem->getProduct()->getData('measurement_sold_in_size');
            if ((!$soldInSize && !is_numeric($soldInSize)) || $soldInSize == false) {
                $soldInSize = 1;
            }
            $totalQty = $soldInSize * $this->proxyItem->getQty();
            $request->setQty($totalQty);
        } else {
            $soldInSize = $this->proxyItem->getProduct()->getData('measurement_sold_in_size');
            if ((!$soldInSize && !is_numeric($soldInSize)) || $soldInSize == false) {
                $soldInSize = 1;
            }
            $totalQty = $soldInSize * $this->proxyItem->getQty();
            $logger->info('Get Qty else: ' . $this->proxyItem->getQty());
            $logger->info('Total Qty else: ' . $totalQty);
            $request->setQty($totalQty);
        }

        // grouped and downloadable products doesn't have options
        if ($this->proxyItem->getMagentoProduct()->isGroupedType() ||
            $this->proxyItem->getMagentoProduct()->isDownloadableType()) {
            return $request;
        }

        /** @var $magentoProduct \Ess\M2ePro\Model\Magento\Product */
        $magentoProduct = $this->modelFactory->getObject('Magento\Product')->setProduct($this->getProduct());
        $options = $this->proxyItem->getOptions();

        if (empty($options)) {
            return $request;
        }

        if ($magentoProduct->isSimpleType()) {
            $logger->info('isSimpleType');
            $request->setOptions($options);
        } elseif ($magentoProduct->isBundleType()) {
            $logger->info('isBundleType');
            $request->setBundleOption($options);
        } elseif ($magentoProduct->isConfigurableType()) {
            $logger->info('isConfigurableType');
            $request->setSuperAttribute($options);
        } elseif ($magentoProduct->isDownloadableType()) {
            $logger->info('isDownloadableType');
            $request->setLinks($options);
        }
        $logger->info('//////////// End Rave Item.php /////////////////');

        return $request;
    }

    // ---------------------------------------

    public function getGiftMessageId()
    {
        $giftMessage = $this->getGiftMessage();

        return $giftMessage ? $giftMessage->getId() : null;
    }

    //########################################

    public function getGiftMessage()
    {
        if ($this->giftMessage !== null) {
            return $this->giftMessage;
        }

        $giftMessageData = $this->proxyItem->getGiftMessage();

        if (!is_array($giftMessageData)) {
            return null;
        }

        $giftMessageData['customer_id'] = (int)$this->quote->getCustomerId();
        /** @var $giftMessage Message */
        $giftMessage = $this->messageFactory->create()->addData($giftMessageData);

        if ($giftMessage->isMessageEmpty()) {
            return null;
        }

        $this->giftMessage = $giftMessage->save();

        return $this->giftMessage;
    }

    public function getAdditionalData(\Magento\Quote\Model\Quote\Item $quoteItem)
    {
        $additionalData = $this->proxyItem->getAdditionalData();
        $existAdditionalData = is_string($quoteItem->getAdditionalData())
            ? $this->getHelper('Data')->unserialize($quoteItem->getAdditionalData())
            : [];

        return $this->getHelper('Data')->serialize(array_merge($existAdditionalData, $additionalData));
    }

    //########################################

    private function getProductTaxClassId()
    {
        $proxyOrder = $this->proxyItem->getProxyOrder();
        $itemTaxRate = $this->proxyItem->getTaxRate();
        $isOrderHasTax = $this->proxyItem->getProxyOrder()->hasTax();
        $hasRatesForCountry = $this->taxHelper->hasRatesForCountry($this->quote->getShippingAddress()->getCountryId());
        $calculationBasedOnOrigin = $this->taxHelper->isCalculationBasedOnOrigin($this->quote->getStore());

        if ($proxyOrder->isTaxModeNone()
            || ($proxyOrder->isTaxModeChannel() && $itemTaxRate <= 0)
            || ($proxyOrder->isTaxModeMagento() && !$hasRatesForCountry && !$calculationBasedOnOrigin)
            || ($proxyOrder->isTaxModeMixed() && $itemTaxRate <= 0 && $isOrderHasTax)
        ) {
            return \Ess\M2ePro\Model\Magento\Product::TAX_CLASS_ID_NONE;
        }

        if ($proxyOrder->isTaxModeMagento()
            || $itemTaxRate <= 0
            || $itemTaxRate == $this->getProductTaxRate()
        ) {
            return $this->getProduct()->getTaxClassId();
        }

        // Create tax rule according to channel tax rate
        // ---------------------------------------
        /** @var $taxRuleBuilder \Ess\M2ePro\Model\Magento\Tax\Rule\Builder */
        $taxRuleBuilder = $this->modelFactory->getObject('Magento_Tax_Rule_Builder');
        $taxRuleBuilder->buildProductTaxRule(
            $itemTaxRate,
            $this->quote->getShippingAddress()->getCountryId(),
            $this->quote->getCustomerTaxClassId()
        );

        $taxRule = $taxRuleBuilder->getRule();

        $productTaxClasses = $taxRule->getProductTaxClasses();
        // ---------------------------------------

        return array_shift($productTaxClasses);
    }

    //########################################

    private function getProductTaxRate()
    {
        /** @var $taxCalculator Calculation */
        $taxCalculator = $this->calculation;

        $request = $taxCalculator->getRateRequest(
            $this->quote->getShippingAddress(),
            $this->quote->getBillingAddress(),
            $this->quote->getCustomerTaxClassId(),
            $this->quote->getStore()
        );
        $request->setProductClassId($this->getProduct()->getTaxClassId());

        return $taxCalculator->getRate($request);
    }

    /**
     * @return Product|null
     * @throws Exception
     */
    public function getProduct()
    {
        if ($this->product !== null) {
            return $this->product;
        }

        if ($this->proxyItem->getMagentoProduct()->isGroupedType() && !$this->proxyItem->pretendedToBeSimple()) {
            $this->product = $this->getAssociatedGroupedProduct();

            if ($this->product === null) {
                throw new Exception('There are no associated Products found for Grouped Product.');
            }
        } else {
            $this->product = $this->proxyItem->getProduct();

            if ($this->proxyItem->getMagentoProduct()->isBundleType()) {
                $this->product->setPriceType(AbstractType::CALCULATE_PARENT);
            }
        }

        // tax class id should be set before price calculation
       // $this->product->setTaxClassId($this->getProductTaxClassId());

        return $this->product;
    }

    //########################################

    private function getAssociatedGroupedProduct()
    {
        $associatedProducts = $this->proxyItem->getAssociatedProducts();
        $associatedProductId = reset($associatedProducts);

        $product = $this->productFactory->create()
            ->setStoreId($this->quote->getStoreId())
            ->load($associatedProductId);

        return $product->getId() ? $product : null;
    }

    //########################################
}
