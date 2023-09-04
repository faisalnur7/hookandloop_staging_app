<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Ebay\Listing\Product\Category\Settings;

use Ess\M2ePro\Block\Adminhtml\Magento\Form\AbstractContainer;

class Mode extends AbstractContainer
{
    public const MODE_SAME = 'same';
    public const MODE_CATEGORY = 'category';
    public const MODE_MANUALLY = 'manually';
    public const MODE_PRODUCT = 'product';

    //########################################

    public function _construct()
    {
        parent::_construct();

        $this->_controller = 'adminhtml_ebay_listing_product_category_settings';
        $this->_mode = 'mode';

        $this->setId('ebayListingCategoryMode');

        $this->removeButton('delete');
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');

        $this->_headerText = $this->__('Set Category');

        $url = $this->getUrl('*/ebay_listing_product_add/deleteAll', ['_current' => true]);

        if (!$this->getRequest()->getParam('without_back')) {
            $this->addButton('back', [
                'label' => $this->__('Back'),
                'class' => 'back',
                'onclick' => 'setLocation(\'' . $url . '\');',
            ]);
        }

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
            'label' => $this->__('Continue'),
            'class' => 'action-primary forward',
            'onclick' => "$('categories_mode_form').submit();",
        ]);
    }

    //########################################

    protected function _toHtml()
    {
        $this->jsTranslator->addTranslations([
            'Apply Settings' => $this->__('Apply Settings'),
        ]);

        /** @var \Ess\M2ePro\Model\Listing $listing */
        $listing = $this->parentFactory->getCachedObjectLoaded(
            \Ess\M2ePro\Helper\Component\Ebay::NICK,
            'Listing',
            $this->getRequest()->getParam('id')
        );

        $this->js->addOnReadyJs(
            <<<JS
require([
    'M2ePro/Ebay/Listing/Product/Category/Settings/Mode'
], function(){

    window.EbayListingProductCategorySettingsModeObj = new EbayListingProductCategorySettingsMode(
        '{$this->getData('mode')}'
    );

});
JS
        );

        $viewHeaderBlock = $this->getLayout()->createBlock(
            \Ess\M2ePro\Block\Adminhtml\Listing\View\Header::class,
            '',
            ['data' => ['listing' => $listing]]
        );

        return $viewHeaderBlock->toHtml() . parent::_toHtml() . <<<HTML
<div id="mode_same_remember_pop_up_content" style="display: none">
        {$this->__(
                'If you continue the Settings you will choose next will be applied to the current M2E Pro Listing
            and automatically assigned to all Products added later.<br/><br/>'
            )}
</div>
HTML;
    }

    //########################################
}
