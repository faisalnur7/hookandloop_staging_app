<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Template\ProductType;

class Assign extends \Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Template\ProductType
{
    /** @var \Ess\M2ePro\Model\Amazon\Template\ProductTypeFactory */
    private $productTypeFactory;
    /** @var \Ess\M2ePro\Helper\Module\Support */
    private $supportHelper;
    /** @var \Ess\M2ePro\Helper\Component\Amazon\Variation */
    private $variationHelper;

    public function __construct(
        \Ess\M2ePro\Model\Amazon\Template\ProductTypeFactory $productTypeFactory,
        \Ess\M2ePro\Helper\Module\Support $supportHelper,
        \Ess\M2ePro\Helper\Component\Amazon\Variation $variationHelper,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Amazon\Factory $amazonFactory,
        \Ess\M2ePro\Model\ResourceModel\Amazon\Listing\Product $amazonListingProductResource,
        \Ess\M2ePro\Model\Amazon\Template\ProductType\DiffFactory $diffFactory,
        \Ess\M2ePro\Model\Amazon\Template\ProductType\ChangeProcessorFactory $changeProcessorFactory,
        \Ess\M2ePro\Model\ResourceModel\Listing\Product\CollectionFactory $listingProductCollectionFactory,
        \Ess\M2ePro\Model\Amazon\Template\ProductTypeFactory $productTypeSettingsFactory,
        \Ess\M2ePro\Model\ResourceModel\Amazon\Template\ProductType $productTypeResource,
        \Ess\M2ePro\Model\Amazon\Template\ProductType\SnapshotBuilderFactory $snapshotBuilderFactory,
        \Ess\M2ePro\Controller\Adminhtml\Context $context
    ) {
        parent::__construct(
            $transactionFactory,
            $amazonFactory,
            $amazonListingProductResource,
            $diffFactory,
            $changeProcessorFactory,
            $listingProductCollectionFactory,
            $productTypeSettingsFactory,
            $productTypeResource,
            $snapshotBuilderFactory,
            $context
        );

        $this->productTypeFactory = $productTypeFactory;
        $this->supportHelper = $supportHelper;
        $this->variationHelper = $variationHelper;
    }

    /**
     * @inheridoc
     */
    public function execute()
    {
        $productsIds = $this->getRequest()->getParam('products_ids');
        $templateId = $this->getRequest()->getParam('product_type_id');
        $isGeneralIdOwnerWillBeSet = (bool)$this->getRequest()->getParam('is_general_id_owner_will_be_set', false);

        if (empty($productsIds) || empty($templateId)) {
            $this->setAjaxContent('You should provide correct parameters.', false);

            return $this->getResult();
        }

        $productType = $this->productTypeFactory
            ->create()
            ->load((int)$templateId);
        if (!$productType->getId()) {
            $this->setAjaxContent('You should provide correct product_type_id.', false);

            return $this->getResult();
        }

        if (!is_array($productsIds)) {
            $productsIds = explode(',', $productsIds);
        }

        $responseType = 'success';
        $messages = [];

        $filteredProductsIdsByType = $this->variationHelper->filterProductsByMagentoProductType($productsIds);
        if (count($productsIds) !== count($filteredProductsIdsByType)) {
            $responseType = 'warning';
            $text = $this->__(
                'Product Type cannot be assigned because %count% Items are Simple
                 with Custom Options or Bundle Magento Products.',
                count($productsIds) - count($filteredProductsIdsByType)
            );

            $messages[] = [
                'type' => 'warning',
                'text' => $text,
            ];
        }

        if ($productType->getNick() !== \Ess\M2ePro\Model\Amazon\Template\ProductType::GENERAL_PRODUCT_TYPE_NICK) {
            $productIdsWithAvailableWorldwideIds =
                $this->variationHelper->filterProductsByAvailableWorldwideIdentifiers($filteredProductsIdsByType);

            if (count($filteredProductsIdsByType) !== count($productIdsWithAvailableWorldwideIds)) {
                $url = $this->supportHelper->getSupportUrl('/support/solutions/articles/9000226680');
                $text = __(
                    <<<HTML
UPC/EAN is missing for %count product(s).
Please configure Product Identifiers settings before adding or updating your Amazon Items.
For more information, please see <a href="%url" target="_blank" class="external-link">this article</a>.
HTML
                    ,
                    [
                        'url' => $url,
                        'count' => count($filteredProductsIdsByType) - count($productIdsWithAvailableWorldwideIds),
                    ]
                );

                $messages[] = [
                    'type' => 'error',
                    'text' => $text,
                ];
            }
        }

        if (empty($filteredProductsIdsByType)) {
            $this->setJsonContent([
                'type' => $responseType,
                'messages' => $messages,
            ]);

            return $this->getResult();
        }

        $this->setProductTypeForProducts($filteredProductsIdsByType, $templateId, $isGeneralIdOwnerWillBeSet);
        $this->runProcessorForParents($filteredProductsIdsByType);

        $text = $this->__(
            'Product Type was assigned to %count% Products',
            count($filteredProductsIdsByType)
        );
        $messages[] = [
            'type' => 'success',
            'text' => $text,
        ];

        $this->setJsonContent([
            'type' => $responseType,
            'messages' => $messages,
            'products_ids' => implode(',', $filteredProductsIdsByType),
        ]);

        return $this->getResult();
    }
}
