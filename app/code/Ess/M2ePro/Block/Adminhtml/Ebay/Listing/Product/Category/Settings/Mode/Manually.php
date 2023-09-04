<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Ebay\Listing\Product\Category\Settings\Mode;

/**
 * Class \Ess\M2ePro\Block\Adminhtml\Ebay\Listing\Product\Category\Settings\Mode\Manually
 */
class Manually extends \Ess\M2ePro\Block\Adminhtml\Magento\Grid\AbstractContainer
{
    //########################################

    public function _construct()
    {
        parent::_construct();

        $this->setId('ebayListingCategoryManually');
        $this->_controller = 'adminhtml_ebay_listing_product_category_settings_mode_manually';

        $this->_headerText = $this->__('Set Category (manually)');

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        $url = $this->getUrl('*/*/', ['step' => 1, '_current' => true]);
        $this->addButton('back', [
            'label' => $this->__('Back'),
            'class' => 'back',
            'onclick' => 'setLocation(\'' . $url . '\');',
        ]);

        $url = $this->getUrl(
            '*/ebay_listing_product_add/exitToListing',
            ['id' => $this->getRequest()->getParam('id')]
        );
        $confirm =
            '<strong>' . $this->__('Are you sure?') . '</strong><br><br>'
            . $this->__('All unsaved changes will be lost and you will be returned to the Listings grid.');
        $this->addButton(
            'exit_to_listing',
            [
                'label' => $this->__('Cancel'),
                'onclick' => "confirmSetLocation('$confirm', '$url');",
                'class' => 'action-primary',
            ]
        );

        $this->addButton('next', [
            'id' => 'ebay_listing_category_continue_btn',
            'class' => 'action-primary forward',
            'label' => $this->__('Continue'),
            'onclick' => 'EbayListingProductCategorySettingsModeProductGridObj.completeCategoriesDataStep(1, 0);',
        ]);
    }

    public function getGridHtml()
    {
        /** @var \Ess\M2ePro\Model\Listing $listing */
        $listing = $this->parentFactory->getCachedObjectLoaded(
            \Ess\M2ePro\Helper\Component\Ebay::NICK,
            'Listing',
            $this->getRequest()->getParam('id')
        );

        $viewHeaderBlock = $this->getLayout()->createBlock(
            \Ess\M2ePro\Block\Adminhtml\Listing\View\Header::class,
            '',
            [
                'data' => ['listing' => $listing],
            ]
        );

        return $viewHeaderBlock->toHtml() . parent::getGridHtml();
    }

    protected function _toHtml()
    {
        $parentHtml = parent::_toHtml();
        $popupsHtml = $this->getPopupsHtml();

        return <<<HTML
<div id="products_progress_bar"></div>
<div id="products_container">{$parentHtml}</div>
<div style="display: none">{$popupsHtml}</div>
HTML;
    }

    //########################################

    private function getPopupsHtml()
    {
        return $this->getLayout()
                    ->createBlock(
                        \Ess\M2ePro\Block\Adminhtml\Ebay\Listing\Product\Category\Settings\Mode\WarningPopup::class
                    )
                    ->toHtml();
    }

    //########################################
}