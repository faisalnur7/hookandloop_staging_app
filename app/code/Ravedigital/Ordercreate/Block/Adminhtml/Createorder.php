<?php
namespace Ravedigital\Ordercreate\Block\Adminhtml;

class Createorder extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }
    
    public function getTempl()
    {
        return __("");
    }
}
