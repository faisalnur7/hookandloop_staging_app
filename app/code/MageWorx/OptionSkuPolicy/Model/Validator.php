<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionSkuPolicy\Model;

use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use MageWorx\OptionBase\Api\ValidatorInterface;
use MageWorx\OptionSkuPolicy\Helper\Data as Helper;

class Validator implements ValidatorInterface
{
    protected Helper $helper;
    protected SkuPolicy $skuPolicy;
    protected ProductResource $productResource;

    /**
     * Validator constructor.
     *
     * @param Helper $helper
     * @param SkuPolicy $skuPolicy
     * @param ProductResource $productResource
     */
    public function __construct(
        Helper $helper,
        SkuPolicy $skuPolicy,
        ProductResource $productResource
    ) {
        $this->helper             = $helper;
        $this->skuPolicy          = $skuPolicy;
        $this->productResource    = $productResource;
    }

    /**
     * Run validation process for add to cart action
     *
     * @param DefaultType $subject
     * @param array $values
     * @return bool
     */
    public function canValidateAddToCart($subject, $values)
    {
        if ($this->helper->isEnabledSkuPolicy()) {

            $skuPolicy = $this->getSkuPolicy($subject->getProduct(), $subject->getOption());

            if ($this->isSkipValidationProcess($skuPolicy, $values)) {
                $optionSku = $subject->getOption()->getSku();
                if ($optionSku && $this->productResource->getIdBySku($optionSku)) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Whether skip add to cart validation
     *
     * @param string $skuPolicy
     * @param array $values
     * @return bool
     */
    protected function isSkipValidationProcess($skuPolicy, $values)
    {
        if ($skuPolicy == Helper::SKU_POLICY_INDEPENDENT || $skuPolicy == Helper::SKU_POLICY_GROUPED) {
            if ($this->skuPolicy->getIsSubmitQuoteFlag() || $values) {
                return true;
            }
        }

        return false;
    }

    /**
     * Run validation process for cart and checkout
     *
     * @param ProductInterface $product
     * @param CustomOptionInterface $option
     * @return bool
     */
    public function canValidateCartCheckout($product, $option)
    {
        if (!$this->helper->isEnabledSkuPolicy()) {
            return true;
        }
        $skuPolicy = $this->getSkuPolicy($product, $option);

        if ($skuPolicy == Helper::SKU_POLICY_INDEPENDENT || $skuPolicy == Helper::SKU_POLICY_GROUPED) {
            return false;
        }

        return true;
    }

    /**
     * Get SKU policy for validation
     *
     * @param ProductInterface $product
     * @param CustomOptionInterface $option
     * @return string
     */
    protected function getSkuPolicy($product, $option)
    {
        $skuPolicy = $option->getSkuPolicy();
        if ($option->getSkuPolicy() == Helper::SKU_POLICY_USE_CONFIG) {
            if ($product->getSkuPolicy() == Helper::SKU_POLICY_USE_CONFIG) {
                $skuPolicy = $this->helper->getDefaultSkuPolicy();
            } else {
                $skuPolicy = $product->getSkuPolicy();
            }
        }

        return $skuPolicy;
    }
}
