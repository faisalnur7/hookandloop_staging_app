<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ravedigital\M2ePro\Model\Magento\Quote;

class Item extends \Ess\M2ePro\Model\Magento\Quote\Item
{
    protected $taxHelper;

    protected $productFactory;

    protected $calculation;

    protected $messageFactory;

    /** @var \Magento\Quote\Model\Quote */
    protected $quote;

    /** @var \Ess\M2ePro\Model\Order\Item\Proxy */
    protected $proxyItem;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var \Magento\GiftMessage\Model\Message */
    protected $giftMessage;

    protected $_productTypeConfigurable;
    protected $_productCatalog;

    //########################################

    public function __construct(
        \Ess\M2ePro\Helper\Factory $helperFactory,
        \Ess\M2ePro\Model\Magento\Tax\Helper $taxHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\Quote\Model\Quote $quote,
        \Ess\M2ePro\Model\Order\Item\Proxy $proxyItem,
        \Ess\M2ePro\Model\Factory $modelFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Model\Product $_productCatalog
    ) {
        $this->taxHelper = $taxHelper;
        $this->productFactory = $productFactory;
        $this->calculation = $calculation;
        $this->messageFactory = $messageFactory;
        $this->quote = $quote;
        $this->proxyItem = $proxyItem;
        $this->_productTypeConfigurable = $productTypeConfigurable;
        $this->_productCatalog =$_productCatalog;
        parent::__construct($taxHelper, $productFactory, $calculation, $messageFactory, $quote, $proxyItem, $helperFactory, $modelFactory);
    }

    //########################################

    /**
     * @return \Magento\Catalog\Model\Product|null
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function getProduct()
    {
        if (!is_null($this->product)) {
            return $this->product;
        }

        if ($this->proxyItem->getMagentoProduct()->isGroupedType()) {
            $this->product = $this->getAssociatedGroupedProduct();

            if (is_null($this->product)) {
                throw new \Ess\M2ePro\Model\Exception('There are no associated Products found for Grouped Product.');
            }
        } else {
            $this->product = $this->proxyItem->getProduct();

            if ($this->proxyItem->getMagentoProduct()->isBundleType()) {
                $this->product->setPriceType(\Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_PARENT);
            }
        }

        // tax class id should be set before price calculation
        $this->product->setTaxClassId($this->getProductTaxClassId());

        return $this->product;
    }

    // ---------------------------------------

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
        $taxRuleBuilder = $this->modelFactory->getObject('Magento\Tax\Rule\Builder');
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

    private function getProductTaxRate()
    {
        /** @var $taxCalculator \Magento\Tax\Model\Calculation */
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

    //########################################

    public function getRequest()
    {
        $request = new \Magento\Framework\DataObject();
        
        if ($this->proxyItem->getMagentoProduct()->isConfigurableType() == true) {
            $configurable = $this->_productTypeConfigurable->setProduct($this->proxyItem->getProduct());

            $superAttribute = $this->proxyItem->getOptions();
            $childProduct = $configurable->getProductByAttributes($superAttribute);
            $child = $this->_productCatalog->setStoreId(1)->load($childProduct->getId());

            if ($child) {
                $soldInSize = $child->getData('measurement_sold_in_size');
                if (!$soldInSize && !is_numeric($soldInSize)) {
                    $soldInSize = 1;
                }
                $totalQty = $soldInSize * $this->proxyItem->getQty();
                $request->setQty($totalQty);
            } else {
                $soldInSize = $this->proxyItem->getProduct()->getData('measurement_sold_in_size');
                if (!$soldInSize && !is_numeric($soldInSize)) {
                    $soldInSize = 1;
                }
                $totalQty = $soldInSize * $this->proxyItem->getQty();
                $request->setQty($totalQty);
            }
        } else {
            $soldInSize = $this->proxyItem->getProduct()->getData('measurement_sold_in_size');
            if (!$soldInSize && !is_numeric($soldInSize)) {
                $soldInSize = 1;
            }
            $totalQty = $soldInSize * $this->proxyItem->getQty();
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
            $request->setOptions($options);
        } elseif ($magentoProduct->isBundleType()) {
            $request->setBundleOption($options);
        } elseif ($magentoProduct->isConfigurableType()) {
            $request->setSuperAttribute($options);
        } elseif ($magentoProduct->isDownloadableType()) {
            $request->setLinks($options);
        }

        return $request;
    }

    //########################################

    public function getGiftMessageId()
    {
        $giftMessage = $this->getGiftMessage();

        return $giftMessage ? $giftMessage->getId() : null;
    }

    public function getGiftMessage()
    {
        if (!is_null($this->giftMessage)) {
            return $this->giftMessage;
        }

        $giftMessageData = $this->proxyItem->getGiftMessage();

        if (!is_array($giftMessageData)) {
            return NULL;
        }

        $giftMessageData['customer_id'] = (int)$this->quote->getCustomerId();
        /** @var $giftMessage \Magento\GiftMessage\Model\Message */
        $giftMessage = $this->messageFactory->create()->addData($giftMessageData);

        if ($giftMessage->isMessageEmpty()) {
            return NULL;
        }

        $this->giftMessage = $giftMessage->save();

        return $this->giftMessage;
    }

    //########################################

    public function getAdditionalData(\Magento\Quote\Model\Quote\Item $quoteItem)
    {
        $additionalData = $this->proxyItem->getAdditionalData();

        $existAdditionalData = $quoteItem->getAdditionalData();
        $existAdditionalData = is_string($existAdditionalData) ? @unserialize($existAdditionalData) : [];

        return serialize(array_merge((array)$existAdditionalData, $additionalData));
    }

    //########################################
}
