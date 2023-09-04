<?php
namespace Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User;

use Magento\Backend\Block\Widget\Form;

/**
 * Class EditPlugin
 * @package Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User
 */
class EditPlugin
{
    /**
     * Set enctype attribute to the form
     *
     * @param Form $subject
     */
    public function beforeGetFormHtml(
        Form $subject
    ) {
        $subject->getForm()->setData('enctype', 'multipart/form-data');
    }
}
