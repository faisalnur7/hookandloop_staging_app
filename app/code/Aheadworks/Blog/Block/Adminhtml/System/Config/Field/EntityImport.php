<?php
namespace Aheadworks\Blog\Block\Adminhtml\System\Config\Field;

use Aheadworks\Blog\ViewModel\Admin\System\Config\Import;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class EntityImport
 */
class EntityImport extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::system/config/entity_import_type.phtml';

    /**
     * EntityImport constructor.
     * @param Context $context
     * @param array $data
     * @param Import $viewModel
     */
    public function __construct(
        Context $context,
        Import $viewModel,
        array $data = []
    ) {
        $data['view_model'] = $viewModel;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}