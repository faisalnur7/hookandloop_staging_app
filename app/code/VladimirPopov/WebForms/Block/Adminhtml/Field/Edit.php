<?php
/**
 * @author      Vladimir Popov
 * @copyright   Copyright © 2020 Vladimir Popov. All rights reserved.
 */

namespace VladimirPopov\WebForms\Block\Adminhtml\Field;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use VladimirPopov\WebForms\Helper\Data;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    protected $webformsHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $webformsHelper
     * @param array $data
     */
    public function __construct(
        Context  $context,
        Registry $registry,
        Data     $webformsHelper,
        array    $data = []
    )
    {
        $this->_coreRegistry  = $registry;
        $this->webformsHelper = $webformsHelper;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('webforms_form')->getId()) {
            return __("Edit Field '%1'", $this->escapeHtml($this->_coreRegistry->registry('webforms_field')->getName()));
        } else {
            return __('New Field');
        }
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/form/edit', ['id' => $this->_coreRegistry->registry('webforms_form')->getId(), 'active_tab' => 'fields_section', 'store' => $this->getRequest()->getParam('store')]);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'field_id';
        $this->_blockGroup = 'VladimirPopov_WebForms';
        $this->_controller = 'adminhtml_field';

        parent::_construct();

        if ($this->webformsHelper->isAllowed($this->_coreRegistry->registry('webforms_form')->getId())) {

            $this->buttonList->update('save', 'label', __('Save Field'));

            if (strstr((string)$this->_coreRegistry->registry('webforms_field')->getType(), 'select')) {
                $this->buttonList->add('logic', array(
                    'label' => __('Add Logic'),
                    'onclick' => 'setLocation(\'' . $this->getAddLogicUrl() . '\')',
                ));
            }

            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('VladimirPopov_WebForms::form_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Field'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    public function getAddLogicUrl()
    {
        return $this->getUrl('*/logic/new', array('field_id' => $this->_coreRegistry->registry('webforms_field')->getId()));
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
