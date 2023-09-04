<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Model;

use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionInventory\Helper\Data as HelperData;
use MageWorx\OptionInventory\Model\ResourceModel\Product\Option\Value\CollectionFactory as OptionValueCollectionFactory;

/**
 * Validator model
 *
 * @package MageWorx\OptionInventory\Model
 */
class Validator extends \Magento\Framework\Model\AbstractModel
{
    protected \Magento\Framework\ObjectManagerInterface $objectManager;
    protected \MageWorx\OptionInventory\Helper\Stock $stockHelper;
    protected OptionValueCollectionFactory $optionValueCollectionFactory;
    protected HelperData $helperData;
    protected BaseHelper $baseHelper;

    /**
     * Validator constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OptionInventory\Helper\Stock $stockHelper
     * @param OptionValueCollectionFactory $optionValueCollectionFactory
     * @param HelperData $helperData
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\OptionInventory\Helper\Stock $stockHelper,
        OptionValueCollectionFactory $optionValueCollectionFactory,
        HelperData $helperData,
        BaseHelper $baseHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectManager                = $objectManager;
        $this->stockHelper                  = $stockHelper;
        $this->optionValueCollectionFactory = $optionValueCollectionFactory;
        $this->helperData                   = $helperData;
        $this->baseHelper                   = $baseHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Validate Requested with Original data
     *
     * @param array $requestedData Requested Option Values
     * @param array $originData Original Option Values
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(array $requestedData, array $originData): void
    {
        foreach ($requestedData as $requestedValue) {
            $originValue = isset($originData[$requestedValue->getId()]) ? $originData[$requestedValue->getId()] : null;
            if (!$this->isAllow($requestedValue, $originValue)) {
                $this->addError($originValue, $requestedValue);
            }
        }
    }

    /**
     * Check if allow original qty add requested qty
     *
     * @param \Magento\Framework\DataObject $requestedValue
     * @param \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $originValue
     * @return bool
     */
    protected function isAllow(
        \Magento\Framework\DataObject $requestedValue,
        \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $originValue
    ): bool {
        if (!$originValue) {
            return true;
        }

        if (!$originValue->getManageStock()) {
            return true;
        }

        if ($originValue->getQty() <= 0) {
            return false;
        }

        if ($requestedValue->getQty() > $originValue->getQty()) {
            return false;
        }

        return true;
    }

    /**
     * Throw exception
     *
     * @param \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value
     * @param \Magento\Framework\DataObject $requestedValue
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addError(
        \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value,
        \Magento\Framework\DataObject $requestedValue
    ): void {
        $this->correctData($value);

        if ($value->getProductId()) {
            $formattedQty = $this->stockHelper->isfloatingQty((int)$value->getProductId())
                ? (float)$value->getQty()
                : (int)$value->getQty();
        } else {
            $formattedQty = $value->getQty();
        }
        $e = new \Magento\Framework\Exception\LocalizedException(
            __(
                'We don\'t have as many  "%1" : "%2" - "%3"  as you requested (available qty: "%4").',
                $requestedValue->getName(),
                $requestedValue->getOptionTitle(),
                $requestedValue->getValueTitle(),
                $formattedQty
            )
        );
        throw $e;
    }

    /**
     * Correct some option value fields.
     * For example: 'title' - can be origin or use product name linked by sku.
     *
     * SkuIsValid - this property set the OptionLink module.
     *
     * @param \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value
     */
    protected function correctData(\MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value): void
    {
        if ($value->getSkuIsValid()) {
            /** @var \MageWorx\OptionInventory\Model\ResourceModel\Product\Option\Value\Collection $valuesCollection */
            $valuesCollection = $this->optionValueCollectionFactory->create();

            $valuesCollection
                ->addTitleToResult(1)
                ->getValuesByOption($value->getId());

            $item = $valuesCollection->getFirstItem();
            $value->setValueTitle($item->getTitle());
        }
    }

    /**
     * This function checks from where to take away quantity.
     *
     * @param \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value
     * @return string
     */
    public function getItemType(\MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface $value): string
    {
        $optionType  = 'option';
        $productType = 'product';

        if (!isset($value['sku_is_valid'])) {
            return $optionType;
        }

        $skuIsValid = $value['sku_is_valid'];

        if ($skuIsValid) {
            return $productType;
        }

        return $optionType;
    }

    /**
     * Run validation process for add to cart action
     *
     * @param \Magento\Framework\DataObject $subject
     * @param array $values
     * @return bool
     */
    public function canValidateAddToCart(
        \Magento\Framework\DataObject $subject,
        array $values
    ): bool {
        return $this->process($subject->getOption());
    }

    /**
     * Run validation process for cart and checkout
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \ Magento\Catalog\Api\Data\ProductOptionInterface
     * @return bool
     */
    public function canValidateCartCheckout(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
    ): bool {
        $value = $this->baseHelper->getInfoBuyRequest($product);
        if (!$value) {
            return true;
        }
        $values = isset($value['options']) ? $value['options'] : [];

        return $this->process($option);
    }

    /**
     * Check out of stock option values, if display out of stok is hidden - skip validation
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     */
    protected function process(\Magento\Catalog\Api\Data\ProductCustomOptionInterface $option): bool
    {

        if ($this->helperData->isDisplayOutOfStockOptions()) {
            return true;
        }

        if ($option->getValues()) {
            foreach ($option->getValues() as $value) {
                if (!$this->stockHelper->isOutOfStockOption($value)) {

                    return true;
                }
            }
        }

        return false;
    }
}
