<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Plugin;

use MageWorx\OptionAdvancedPricing\Helper\Data as Helper;
use MageWorx\OptionAdvancedPricing\Model\SpecialPrice as SpecialPriceModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use Magento\Framework\Json\DecoderInterface;

class ExtendPriceConfig extends \Magento\Catalog\Block\Product\View\Options
{
    protected Helper $helper;
    protected SpecialPriceModel $specialPriceModel;
    protected PriceCurrencyInterface $priceCurrency;
    protected BasePriceHelper $basePriceHelper;
    protected DecoderInterface $jsonDecoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        Helper $helper,
        SpecialPriceModel $specialPriceModel,
        PriceCurrencyInterface $priceCurrency,
        BasePriceHelper $basePriceHelper,
        DecoderInterface $jsonDecoder,
        array $data = []
    ) {
        $this->helper            = $helper;
        $this->specialPriceModel = $specialPriceModel;
        $this->priceCurrency     = $priceCurrency;
        $this->basePriceHelper   = $basePriceHelper;
        $this->jsonDecoder       = $jsonDecoder;
        parent::__construct($context, $pricingHelper, $catalogData, $jsonEncoder, $option, $registry, $arrayUtils);
    }

    /**
     * Extend price config with suitable special price on frontend
     *
     * @param \Magento\Catalog\Model\Product\Type\Price $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetJsonConfig($subject, $proceed)
    {
        if (!$subject->hasOptions()) {
            return $proceed();
        }

        if (!$this->helper->isSpecialPriceEnabled()) {
            return $proceed();
        }

        $defaultConfig  = $this->jsonDecoder->decode($proceed());
        $extendedConfig = $defaultConfig;

        foreach ($subject->getOptions() as $option) {
            /* @var $option \Magento\Catalog\Model\Product\Option */
            $values = $option->getValues();

            if (!empty($values) && $option->hasValues()) {
                foreach ($values as $valueId => $value) {

                    $config          = $this->getExtendedJsonConfig($defaultConfig, $option, $valueId);
                    $config['title'] = $value->getTitle();
                    $specialPrice    = $this->specialPriceModel->getActualSpecialPrice($value, true);
                    $needIncludeTax  = $this->basePriceHelper->getCatalogPriceContainsTax(
                        $this->getProduct()->getStoreId()
                    );
                    $isSpecialPrice  = false;

                    if ($specialPrice !== null) {
                        $basePriceAmount  = $config['prices']['basePrice']['amount'];
                        $finalPriceAmount = $config['prices']['finalPrice']['amount'];
                        if ($needIncludeTax) {
                            $basePriceAmount = min(
                                $basePriceAmount,
                                $specialPrice * ($basePriceAmount / $finalPriceAmount)
                            );
                        } else {
                            $basePriceAmount = min($basePriceAmount, $specialPrice);
                        }
                        $finalPriceAmount = min($finalPriceAmount, $specialPrice);

                        if ($specialPrice <= $finalPriceAmount) {
                            $isSpecialPrice = true;
                        }

                        $basePriceAmount  = $this->basePriceHelper->getTaxPrice(
                            $this->getProduct(),
                            $basePriceAmount,
                            $needIncludeTax
                        );
                        $finalPriceAmount = $this->basePriceHelper->getTaxPrice(
                            $this->getProduct(),
                            $finalPriceAmount,
                            $needIncludeTax || $isSpecialPrice
                        );
                        $config['prices']['basePrice']['amount']  = $basePriceAmount;
                        $config['prices']['finalPrice']['amount'] = $finalPriceAmount;
                    }

                    if ($specialPrice !== null) {
                        $config['special_price_display_node'] = $this->helper->getSpecialPriceDisplayNode(
                            $config['prices'],
                            $this->specialPriceModel->getSpecialPriceItem()
                        );
                    }

                    $extendedConfig[$option->getId()][$valueId] = array_merge(
                        $defaultConfig[$option->getId()][$valueId],
                        $config
                    );
                }
            } else {
                $config          = $this->getExtendedJsonConfig($defaultConfig, $option, null);
                $config['title'] = $option->getTitle();

                $extendedConfig[$option->getId()] = array_merge(
                    $defaultConfig[$option->getId()],
                    $config
                );
            }
        }

        return $this->_jsonEncoder->encode($extendedConfig);
    }

    public function getExtendedJsonConfig($defaultConfig, $option, $valueId)
    {
        $config                = [];
        $defaultConfigOptionId = $defaultConfig[$option->getId()];

        if ($valueId !== null) {
            $config['prices']['oldPrice']['amount'] =
                $defaultConfigOptionId[$valueId]['prices']['oldPrice']['amount'];
        } else {
            $config['prices']['oldPrice']['amount'] =
                $defaultConfigOptionId['prices']['oldPrice']['amount'];
        }

        $config['prices']['oldPrice']['amount_excl_tax'] = $config['prices']['oldPrice']['amount'];
        $config['prices']['oldPrice']['amount_incl_tax'] = $this->basePriceHelper->getTaxPrice(
            $this->getProduct(),
            $config['prices']['oldPrice']['amount'],
            true
        );

        if ($valueId !== null) {
            $config['prices']['basePrice']['amount']  =
                $defaultConfigOptionId[$valueId]['prices']['basePrice']['amount'];
            $config['prices']['finalPrice']['amount'] =
                $defaultConfigOptionId[$valueId]['prices']['finalPrice']['amount'];
        } else {
            $config['prices']['basePrice']['amount']  =
                $defaultConfigOptionId['prices']['basePrice']['amount'];
            $config['prices']['finalPrice']['amount'] =
                $defaultConfigOptionId['prices']['finalPrice']['amount'];
        }

        $config['valuePrice'] = $this->priceCurrency->format(
            $config['prices']['oldPrice']['amount'],
            false
        );

        return $config;
    }
}
