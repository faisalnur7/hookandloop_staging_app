<?php
namespace Ebridge\Integration\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\ObjectManagerInterface;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to block template
     */
    const CHECK_TEMPLATE = 'Ebridge_Integration::system/config/button.phtml';

    public function __construct(Context $context, $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        
        $this->addData(
            [
                'id'        => 'createAccountButton',
                'button_label'     => _('Start Your Integration')
            ]
        );

        return $this->_toHtml();
    }

    protected function getUrlTest()
    {
        //This is your real url you want to redirect when click on button
        window.open("http://ebridgeoauth.azurewebsites.net/magento2/Default.aspx", '_blank');
    }
}
