<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Ebay\Listing\Product\Action\Type\Revise;

class Checker
{
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\TitleFactory */
    private $titleDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\SubtitleFactory */
    private $subtitleDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\DescriptionFactory */
    private $descriptionDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ImagesFactory */
    private $imagesDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\CategoriesFactory */
    private $categoriesDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\PartsFactory */
    private $partsDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ShippingFactory */
    private $shippingDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ReturnPolicyFactory */
    private $returnPolicyDataBuilderFactory;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\OtherFactory */
    private $otherDataBuilderFactory;
    /** @var \Ess\M2ePro\Helper\Data */
    private $helperData;
    /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Action\ConfiguratorFactory */
    private $configuratorFactory;

    public function __construct(
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\ConfiguratorFactory $configuratorFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\TitleFactory $titleDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\SubtitleFactory $subtitleDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\DescriptionFactory $descriptionDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ImagesFactory $imagesDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\CategoriesFactory $categoriesDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\PartsFactory $partsDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ShippingFactory $shippingDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\ReturnPolicyFactory $returnPolicyDataBuilderFactory,
        \Ess\M2ePro\Model\Ebay\Listing\Product\Action\DataBuilder\OtherFactory $otherDataBuilderFactory,
        \Ess\M2ePro\Helper\Data $helperData
    ) {
        $this->titleDataBuilderFactory = $titleDataBuilderFactory;
        $this->subtitleDataBuilderFactory = $subtitleDataBuilderFactory;
        $this->descriptionDataBuilderFactory = $descriptionDataBuilderFactory;
        $this->imagesDataBuilderFactory = $imagesDataBuilderFactory;
        $this->categoriesDataBuilderFactory = $categoriesDataBuilderFactory;
        $this->partsDataBuilderFactory = $partsDataBuilderFactory;
        $this->shippingDataBuilderFactory = $shippingDataBuilderFactory;
        $this->returnPolicyDataBuilderFactory = $returnPolicyDataBuilderFactory;
        $this->otherDataBuilderFactory = $otherDataBuilderFactory;
        $this->helperData = $helperData;
        $this->configuratorFactory = $configuratorFactory;
    }

    public function calculateForManualAction(\Ess\M2ePro\Model\Listing\Product $listingProduct): Checker\Result
    {
        $configurator = $this->configuratorFactory->create();
        $configurator->disableAll();

        $ebayListingProduct = $listingProduct->getChildObject();

        $tags = [];

        if ($this->isQtyReviseEnabled($ebayListingProduct)) {
            $configurator->allowQty()->allowVariations();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_QTY;
        }

        if ($this->isPriceReviseEnabled($ebayListingProduct)) {
            $configurator->allowPrice()->allowVariations();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_PRICE;
        }

        if ($this->isTitleReviseEnabled($ebayListingProduct)) {
            $configurator->allowTitle();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_TITLE;
        }

        if ($this->isSubtitleReviseEnabled($ebayListingProduct)) {
            $configurator->allowSubtitle();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_SUBTITLE;
        }

        if ($this->isDescriptionReviseEnabled($ebayListingProduct)) {
            $configurator->allowDescription();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_DESCRIPTION;
        }

        if ($this->isImagesReviseEnabled($ebayListingProduct)) {
            $configurator->allowImages();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_IMAGES;
        }

        if ($this->isCategoriesReviseEnabled($ebayListingProduct)) {
            $configurator->allowCategories();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_CATEGORIES;
        }

        if ($this->isPartsReviseEnabled($ebayListingProduct)) {
            $configurator->allowParts();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_PARTS;
        }

        if ($this->isShippingReviseEnabled($ebayListingProduct)) {
            $configurator->allowShipping();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_SHIPPING;
        }

        if ($this->isReturnReviseEnabled($ebayListingProduct)) {
            $configurator->allowReturn();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_RETURN;
        }

        if ($this->isOtherReviseEnabled($ebayListingProduct)) {
            $configurator->allowOther();
            $tags[] = \Ess\M2ePro\Model\Ebay\Listing\Product\Action\Configurator::DATA_TYPE_OTHER;
        }

        return new Checker\Result($configurator, $tags);
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForQty(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        if (!$this->isQtyReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $isMaxAppliedValueModeOn = $ebaySynchronizationTemplate->isReviseUpdateQtyMaxAppliedValueModeOn();
        $maxAppliedValue = $ebaySynchronizationTemplate->getReviseUpdateQtyMaxAppliedValue();

        if (!$ebayListingProduct->isVariationsReady()) {
            $productQty = $ebayListingProduct->getQty();
            $channelQty = $ebayListingProduct->getOnlineQty() - $ebayListingProduct->getOnlineQtySold();

            // Check ReviseUpdateQtyMaxAppliedValue
            if ($isMaxAppliedValueModeOn && $productQty > $maxAppliedValue && $channelQty > $maxAppliedValue) {
                return false;
            }

            if ($productQty != $channelQty) {
                return true;
            }
        } else {
            $listingProduct = $ebayListingProduct->getParentObject();
            $variations = $listingProduct->getVariations(true);

            foreach ($variations as $variation) {
                /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Variation $ebayVariation */
                $ebayVariation = $variation->getChildObject();

                $productQty = $ebayVariation->getQty();
                $channelQty = $ebayVariation->getOnlineQty() - $ebayVariation->getOnlineQtySold();

                if (
                    $productQty != $channelQty &&
                    (!$isMaxAppliedValueModeOn || $productQty <= $maxAppliedValue || $channelQty <= $maxAppliedValue)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForPrice(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isPriceReviseEnabled($ebayListingProduct)) {
            return false;
        }

        if ($ebayListingProduct->isVariationsReady()) {
            $listingProduct = $ebayListingProduct->getParentObject();
            $variations = $listingProduct->getVariations(true);

            foreach ($variations as $variation) {
                /** @var \Ess\M2ePro\Model\Ebay\Listing\Product\Variation $ebayVariation */
                $ebayVariation = $variation->getChildObject();

                if ($ebayVariation->getOnlinePrice() != $ebayVariation->getPrice()) {
                    return true;
                }
            }
        } else {
            if (
                $ebayListingProduct->isListingTypeFixed()
                && $ebayListingProduct->getOnlineCurrentPrice() != $ebayListingProduct->getFixedPrice()
            ) {
                return true;
            }

            if ($ebayListingProduct->isListingTypeAuction()) {
                if ($ebayListingProduct->getOnlineStartPrice() != $ebayListingProduct->getStartPrice()) {
                    return true;
                }

                if ($ebayListingProduct->getOnlineReservePrice() != $ebayListingProduct->getReservePrice()) {
                    return true;
                }

                if ($ebayListingProduct->getOnlineBuyItNowPrice() != $ebayListingProduct->getBuyItNowPrice()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForTitle(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isTitleReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->titleDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $actionData = $actionDataBuilder->getBuilderData();

        return $actionData['title'] !== $ebayListingProduct->getOnlineTitle();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForSubtitle(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isSubtitleReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->subtitleDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $actionData = $actionDataBuilder->getBuilderData();

        return $actionData['subtitle'] !== $ebayListingProduct->getOnlineSubTitle();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function isNeedReviseForDescription(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isDescriptionReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->descriptionDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $actionData = $actionDataBuilder->getBuilderData();

        $hashDescription = $this->helperData->md5String($actionData['description']);

        return $hashDescription !== $ebayListingProduct->getOnlineDescription();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function isNeedReviseForImages(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isImagesReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->imagesDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);
        $actionDataBuilder->setIsVariationItem($ebayListingProduct->isVariationsReady());

        $hashImagesData = $this->helperData->md5String(
            \Ess\M2ePro\Helper\Json::encode($actionDataBuilder->getBuilderData())
        );

        return $hashImagesData !== $ebayListingProduct->getOnlineImages();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForCategories(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isCategoriesReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->categoriesDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        return $actionDataBuilder->getBuilderData() != $ebayListingProduct->getOnlineCategoriesData();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    public function isNeedReviseForParts(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isPartsReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->partsDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        return $actionDataBuilder->getHash() != $ebayListingProduct->getData('online_parts_data');
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function isNeedReviseForShipping(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isShippingReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->shippingDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $hashReturnData = $this->helperData->md5String(
            \Ess\M2ePro\Helper\Json::encode($actionDataBuilder->getBuilderData())
        );

        return $hashReturnData !== $ebayListingProduct->getOnlineShippingData();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function isNeedReviseForReturn(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isReturnReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->returnPolicyDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $hashReturnData = $this->helperData->md5String(
            \Ess\M2ePro\Helper\Json::encode($actionDataBuilder->getBuilderData())
        );

        return $hashReturnData !== $ebayListingProduct->getOnlineReturnData();
    }

    /**
     * @param \Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct
     *
     * @return bool
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function isNeedReviseForOther(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        if (!$this->isOtherReviseEnabled($ebayListingProduct)) {
            return false;
        }

        $listingProduct = $ebayListingProduct->getParentObject();

        $actionDataBuilder = $this->otherDataBuilderFactory->create();
        $actionDataBuilder->setListingProduct($listingProduct);

        $hashOtherData = $this->helperData->md5String(
            \Ess\M2ePro\Helper\Json::encode($actionDataBuilder->getBuilderData())
        );

        return $hashOtherData !== $ebayListingProduct->getOnlineOtherData();
    }

    private function isQtyReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateQty();
    }

    private function isPriceReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdatePrice();
    }

    private function isTitleReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateTitle();
    }

    private function isSubtitleReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateSubtitle();
    }

    private function isDescriptionReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateDescription();
    }

    private function isImagesReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateImages();
    }

    private function isCategoriesReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateCategories();
    }

    private function isPartsReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateParts();
    }

    private function isShippingReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateShipping();
    }

    private function isReturnReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateReturn();
    }

    private function isOtherReviseEnabled(\Ess\M2ePro\Model\Ebay\Listing\Product $ebayListingProduct): bool
    {
        $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();

        return $ebaySynchronizationTemplate->isReviseUpdateOther();
    }
}
