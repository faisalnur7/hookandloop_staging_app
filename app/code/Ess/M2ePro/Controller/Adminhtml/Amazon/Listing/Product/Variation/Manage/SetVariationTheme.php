<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Variation\Manage;

use Ess\M2ePro\Controller\Adminhtml\Amazon\Main;

class SetVariationTheme extends Main
{
    /** @var \Ess\M2ePro\Helper\Component\Amazon\ProductType */
    private $productTypeHelper;
    /** @var \Ess\M2ePro\Helper\Component\Amazon\Variation */
    protected $variationHelper;
    /** @var \Ess\M2ePro\Helper\Component\Amazon\Vocabulary */
    protected $vocabularyHelper;

    public function __construct(
        \Ess\M2ePro\Helper\Component\Amazon\ProductType $productTypeHelper,
        \Ess\M2ePro\Helper\Component\Amazon\Variation $variationHelper,
        \Ess\M2ePro\Helper\Component\Amazon\Vocabulary $vocabularyHelper,
        \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Amazon\Factory $amazonFactory,
        \Ess\M2ePro\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($amazonFactory, $context);

        $this->productTypeHelper = $productTypeHelper;
        $this->variationHelper = $variationHelper;
        $this->vocabularyHelper = $vocabularyHelper;
    }

    public function execute()
    {
        $listingProductId = $this->getRequest()->getParam('product_id');
        $variationTheme = $this->getRequest()->getParam('variation_theme', null);

        if (empty($listingProductId) || $variationTheme === null) {
            return $this->getResponse()->setBody('You should provide correct parameters.');
        }

        /** @var \Ess\M2ePro\Model\Listing\Product $listingProduct */
        $listingProduct = $this->amazonFactory->getObjectLoaded('Listing\Product', $listingProductId);

        /** @var \Ess\M2ePro\Model\Amazon\Listing\Product $amazonListingProduct */
        $amazonListingProduct = $listingProduct->getChildObject();

        $parentTypeModel = $amazonListingProduct->getVariationManager()->getTypeModel();

        $additionalData = $listingProduct->getAdditionalData();
        if (
            empty($additionalData['migrated_to_product_types'])
            && $amazonListingProduct->isGeneralIdOwner()
        ) {
            $productType = $amazonListingProduct->getProductTypeTemplate();
            $productTypeNick = $productType->getNick();
            $marketplaceId = $listingProduct->getListing()->getMarketplaceId();
            $dictionary = $this->productTypeHelper->getMarketplaceDictionary($marketplaceId);
            $dictionaryProductTypes = $dictionary->getProductTypes();
            if (!empty($dictionaryProductTypes[$productTypeNick]['variation_themes'][$variationTheme])) {
                $themeData = $dictionaryProductTypes[$productTypeNick]['variation_themes'][$variationTheme];
                $sets = [];

                foreach ($themeData['attributes'] as $attribute) {
                    $sets[$attribute] = [];
                }

                $listingProduct->setSetting(
                    'additional_data',
                    'variation_channel_attributes_sets',
                    $sets
                );
            }
        }

        $result = ['success' => true];

        if ($parentTypeModel->getChannelTheme() == $variationTheme) {
            $this->setJsonContent($result);

            return $this->getResult();
        }

        $parentTypeModel->setChannelTheme($variationTheme, true, false);

        $this->variationHelper->increaseThemeUsageCount($variationTheme, $listingProduct->getMarketplace()->getId());

        $productDataNick = $amazonListingProduct->getProductTypeTemplate()->getNick();

        /** @var \Ess\M2ePro\Model\Amazon\Marketplace\Details $marketplaceDetails */
        $marketplaceDetails = $this->modelFactory->getObject('Amazon_Marketplace_Details');
        $marketplaceDetails->setMarketplaceId($amazonListingProduct->getMarketplace()->getId());

        $themeAttributes = $marketplaceDetails->getVariationThemeAttributes($productDataNick, $variationTheme);
        $productAttributes = $parentTypeModel->getProductAttributes();

        if (count($themeAttributes) != 1 || count($productAttributes) != 1) {
            $parentTypeModel->getProcessor()->process();
            $this->setJsonContent($result);

            return $this->getResult();
        }

        $productAttribute = reset($productAttributes);
        $themeAttribute = reset($themeAttributes);

        $parentTypeModel->setMatchedAttributes([$productAttribute => $themeAttribute], true);
        $parentTypeModel->getProcessor()->process();

        if ($productAttribute == $themeAttribute || $listingProduct->getMagentoProduct()->isGroupedType()) {
            $this->setJsonContent($result);

            return $this->getResult();
        }

        if ($this->vocabularyHelper->isAttributeAutoActionDisabled()) {
            $this->setJsonContent($result);

            return $this->getResult();
        }

        if ($this->vocabularyHelper->isAttributeExistsInLocalStorage($productAttribute, $themeAttribute)) {
            $this->setJsonContent($result);

            return $this->getResult();
        }

        if ($this->vocabularyHelper->isAttributeExistsInServerStorage($productAttribute, $themeAttribute)) {
            $this->setJsonContent($result);

            return $this->getResult();
        }

        if ($this->vocabularyHelper->isAttributeAutoActionNotSet()) {
            $result['vocabulary_attributes'] = [$productAttribute => $themeAttribute];
            $this->setJsonContent($result);

            return $this->getResult();
        }

        $this->vocabularyHelper->addAttribute($productAttribute, $themeAttribute);

        $this->setJsonContent($result);

        return $this->getResult();
    }
}
