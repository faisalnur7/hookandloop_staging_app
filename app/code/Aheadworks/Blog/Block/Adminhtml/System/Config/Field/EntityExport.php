<?php
namespace Aheadworks\Blog\Block\Adminhtml\System\Config\Field;

use Aheadworks\Blog\ViewModel\Admin\System\Config\Export;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class EntityExport
 */
class EntityExport extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::system/config/entity_export_type.phtml';

    /**
     * EntityImport constructor.
     * @param Context $context
     * @param array $data
     * @param Export $viewModel
     */
    public function __construct(
        Context $context,
        Export $viewModel,
        array $data = []
    ) {
        $data['view_model'] = $viewModel;
        parent::__construct($context, $data);
    }

    /**
     * Add child export files grid in layout
     *
     * @return EntityExport
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $filesBlock = $this->getLayout()->createBlock(
            \Magento\Ui\Block\Wrapper::class,
            'blog.export.files'
        )->setTemplate(
            'Aheadworks_Blog::system/config/export/files.phtml'
        )->setData('uiComponent', 'aw_blog_export_files'
        )->setData('additionalClasses', 'aw_blog_export_files');

        $this->setFilesBlock($filesBlock);

        return parent::_beforeToHtml();
    }

    /**
     * @inheritDoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $elementHtml = parent::_getElementHtml($element);
        $this->setData('element_html', $elementHtml);

        return $this->_toHtml();
    }
}