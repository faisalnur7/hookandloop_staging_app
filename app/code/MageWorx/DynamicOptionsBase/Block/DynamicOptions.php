<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\DynamicOptionsBase\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Helper\Data as TaxDataHelper;
use Magento\Tax\Model\Calculation;

class DynamicOptions extends Template
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * @var TaxDataHelper
     */
    protected $taxDataHelper;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $validationCache = [];

    /**
     * @var Calculation
     */
    protected $customerSession;

    /**
     * DynamicOptions constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param Json $jsonSerializer
     * @param Registry $registry
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param HttpContext $httpContext
     * @param Calculation $calculation
     * @param TaxDataHelper $taxDataHelper
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        Json $jsonSerializer,
        Registry $registry,
        \Magento\Catalog\Helper\Data $catalogData,
        HttpContext $httpContext,
        Calculation $calculation,
        TaxDataHelper $taxDataHelper,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->storeManager    = $storeManager;
        $this->jsonSerializer  = $jsonSerializer;
        $this->registry        = $registry;
        $this->catalogData     = $catalogData;
        $this->httpContext     = $httpContext;
        $this->calculation     = $calculation;
        $this->taxDataHelper   = $taxDataHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool|false|mixed|string
     */
    public function getJsonData()
    {
        $product = $this->getProduct();
        if (!$product) {
            return '';
        }

        if (!empty($this->validationCache[$product->getId()])) {
            return $this->validationCache[$product->getId()];
        }

        $options              = $product->getMageworxDynamicOptions();
        $data['options_data'] = [];
        if($options){
            foreach ($options as $option) {
                $data['options_data'][$option->getOptionId()] = $option->getData();
            }
        }
        $taxRates = $this->getCurrentTaxRates($product);

        if ($product->getPricePerUnit()) {
            $pricePerUnit = $this->convertPricePerUnit((string)$product->getPricePerUnit());
            $priceInclTax = $priceExclTax = $pricePerUnit;
            if ($taxRates && is_array($taxRates)) {
                $store                     = $this->storeManager->getStore();
                $isPriceInclTax            = $this->taxDataHelper->priceIncludesTax($store);
                $taxRates                  = array_filter($taxRates);
                if ($isPriceInclTax) {
                    $priceInclTax = $pricePerUnit;
                    $priceExclTax = $this->calculateTaxAmount($pricePerUnit, $taxRates, true);
                } else {
                    $priceInclTax = $this->calculateTaxAmount($pricePerUnit, $taxRates, false);
                    $priceExclTax = $pricePerUnit;
                }
            }
            $data = $this->preparePricePerUnitData($data, $pricePerUnit, $priceInclTax, $priceExclTax);
        } else {
            $data = $this->preparePricePerUnitData($data);
        }

        return $this->validationCache[$product->getId()] = $this->jsonSerializer->serialize($data);
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    protected function getProduct(): ?\Magento\Catalog\Model\Product
    {
        $product = $this->registry->registry('product');
        if (!$product || !$product->getId()) {
            return null;
        }

        return $product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array|null
     */
    protected function getCurrentTaxRates(\Magento\Catalog\Model\Product $product): ?array
    {
        $taxRates = $this->httpContext->getValue('tax_rates');
        if (!$taxRates) {
            $attribute            = $product->getCustomAttribute('tax_class_id');
            $taxClassId           = $attribute ? $attribute->getValue() : null;
            $storeId              = $this->storeManager->getStore()->getId();
            $customerId           = $this->customerSession->getCustomerId();
            $addressRequestObject = $this->calculation->getRateRequest(null, null, null, $storeId, $customerId);

            $addressRequestObject->setProductClassId($taxClassId);
            $appliedRates = $this->calculation->getAppliedRates($addressRequestObject);
            foreach ($appliedRates as $appliedRate) {
                $taxRates[] = $appliedRate['percent'];
            }
        }

        return $taxRates;
    }

    /**
     * @param string $pricePerUnit
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function convertPricePerUnit(string $pricePerUnit): float
    {
        return $this->storeManager->getStore()->getCurrentCurrencyRate() * $pricePerUnit;
    }

    /**
     * @param array $data
     * @param float $price
     * @param float $priceInclTax
     * @param float $priceExclTax
     * @return array
     */
    protected function preparePricePerUnitData(
        array $data,
        float $price = 0,
        float $priceInclTax = 0,
        float $priceExclTax = 0
    ): array {
        $data['price_per_unit'] = [
            'amount'          => $price,
            'amount_incl_tax' => $priceInclTax,
            'amount_excl_tax' => $priceExclTax,
        ];

        return $data;
    }

    /**
     * @param float $pricePerUnit
     * @param array $taxRates
     * @param boolean $isPriceInclTax
     * @return float
     */
    protected function calculateTaxAmount(float $pricePerUnit, array $taxRates, bool $isPriceInclTax): float
    {
        $calculatedTaxPrice = 0;
        foreach ($taxRates as $taxRate) {
            if ($taxRate) {
                $taxAmount          = $this->calculation->calcTaxAmount(
                    $pricePerUnit,
                    $taxRate,
                    $isPriceInclTax,
                    false
                );
                $calculatedTaxPrice += $isPriceInclTax ? -1 * $taxAmount : $taxAmount;
            }
        }

        return $calculatedTaxPrice + $pricePerUnit;
    }
}