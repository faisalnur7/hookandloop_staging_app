<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Add;

class ResetProductTypeTemplate extends \Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Add
{
    public function execute()
    {
        $listingId = $this->getRequest()->getParam('listing_id');

        /** @var \Ess\M2ePro\Model\Listing $listing */
        $listing = $this->amazonFactory->getCachedObjectLoaded('Listing', $listingId);

        $productIds = (array)$listing->getSetting(
            'additional_data',
            'adding_new_asin_listing_products_ids'
        );
        $productIds = $this->variationHelper->filterLockedProducts($productIds);

        $listing->setSetting(
            'additional_data',
            'adding_new_asin_product_type_data',
            []
        );
        $listing->save();
        $this->setProductTypeTemplate($productIds, null);

        $this->setJsonContent([
            'back_url' => $this->getUrl(
                '*/amazon_listing_product_add/index',
                [
                    'id' => $listingId,
                    'step' => 4,
                ]
            ),
        ]);

        return $this->getResult();
    }
}
