<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Walmart\Listing\Product\Add;

class CategoryTemplate extends \Ess\M2ePro\Block\Adminhtml\Magento\Form\AbstractContainer
{
    /** @var  \Ess\M2ePro\Model\Listing */
    protected $listing;

    /** @var \Ess\M2ePro\Helper\Module\Support */
    private $supportHelper;

    /** @var \Ess\M2ePro\Helper\Data\GlobalData */
    private $globalDataHelper;

    public function __construct(
        \Ess\M2ePro\Block\Adminhtml\Magento\Context\Widget $context,
        \Ess\M2ePro\Helper\Module\Support $supportHelper,
        \Ess\M2ePro\Helper\Data\GlobalData $globalDataHelper,
        array $data = []
    ) {
        $this->supportHelper = $supportHelper;
        $this->globalDataHelper = $globalDataHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('walmartListingAddCategoryTemplate');
        $this->_controller = 'adminhtml_walmart_listing_product_add';
        $this->_mode = 'categoryTemplate';
        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
        // ---------------------------------------

        $this->listing = $this->globalDataHelper->getValue('listing_for_products_add');

        $url = $this->getUrl('*/*/removeAddedProducts', [
            'step' => 1,
            '_current' => true,
        ]);
        $this->addButton('back', [
            'label' => __('Back'),
            'class' => 'back',
            'onclick' => 'setLocation(\'' . $url . '\');',
        ]);

        $url = $this->getUrl(
            '*/walmart_listing_product_add/exitToListing',
            ['id' => $this->getRequest()->getParam('id')]
        );
        $confirm =
            '<strong>' . __('Are you sure?') . '</strong><br><br>'
            . __('All unsaved changes will be lost and you will be returned to the Listings grid.');
        $this->addButton(
            'exit_to_listing',
            [
                'label' => __('Cancel'),
                'onclick' => "confirmSetLocation('$confirm', '$url');",
                'class' => 'action-primary',
            ]
        );

        $this->addButton('next', [
            'label' => __('Continue'),
            'class' => 'action-primary forward',
            'onclick' => "categoryTemplateModeFormSubmit()",
        ]);
    }

    protected function _prepareLayout()
    {
        $this->appendHelpBlock([
            'content' => $this->__(
                '<p>On this page, you can assign the relevant Category Policy to the Products
                you are currently adding to M2E Pro Listing. </p>

                <p><strong>Note</strong>: Category Policy is required when you create a new offer on Walmart.</p><br>

                <p>The detailed information can be found
                 <a href="%url%" target="_blank" class="external-link">here</a>.</p>',
                $this->supportHelper->getDocumentationArticleUrl(
                    'help/m2/walmart-integration/m2e-pro-listing-set-up/configuring-policies/category-policy'
                )
            ),
        ]);

        parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        $viewHeaderBlock = $this->getLayout()->createBlock(
            \Ess\M2ePro\Block\Adminhtml\Listing\View\Header::class,
            '',
            ['data' => ['listing' => $this->listing]]
        );

        return $viewHeaderBlock->toHtml() . parent::_toHtml();
    }
}
