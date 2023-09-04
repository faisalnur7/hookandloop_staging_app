<?php
namespace Aheadworks\Bup\Plugin\User\Block\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ElementPlugin
 * @package Aheadworks\Bup\Plugin\User\Block\Form
 */
class ElementPlugin
{
    /**
     * Add validation class for element
     *
     * @param AbstractElement $element
     */
    public function beforeGetHtmlAttributes(
        AbstractElement $element
    ) {
        if ($element->getHtmlId() == 'aw_bup_image_loader') {
            $element->addClass('aw_bup-validate-image');
        }
    }
}
