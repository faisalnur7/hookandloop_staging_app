<?php
namespace Aheadworks\Blog\Block\Adminhtml\Widget\Instance\Edit\Tab\Main;

use Aheadworks\Blog\ViewModel\Admin\Widget\Instance\Edit\Tab\Main\WidgetInstance;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Type;
use Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout as NativeLayout;

class Layout extends NativeLayout
{
    /**
     * Layout constructor.
     * @param Context $context
     * @param Type $productType
     * @param array $data
     * @param WidgetInstance $viewModel
     */
    public function __construct(
        Context $context,
        Type $productType,
        WidgetInstance $viewModel,
        array $data = []
    ) {
        $data['view_model'] = $viewModel;
        parent::__construct(
            $context,
            $productType,
            $data
        );
    }

    /**
     * Retrieve Display On Options
     *
     * @return array
     */
    protected function _getDisplayOnOptions()
    {
        $options = parent::_getDisplayOnOptions();
        $options = array_merge($options, $this->getViewModel()->getDisplayOnOptionsData());

        return $options;
    }

    /**
     * Retrieve Display On Containers
     *
     * @return array
     */
    public function getDisplayOnContainers()
    {
        $containers = parent::getDisplayOnContainers();
        $containers = array_merge($containers, $this->getViewModel()->getDisplayOnContainersData());

        return $containers;
    }

    /**
     * Preparing global layout
     *
     * @return Layout
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'widget_instance_script_init',
            \Aheadworks\Blog\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\WidgetInstanceScript::class
        );

        return parent::_prepareLayout();
    }

    /**
     * Render child block html after self block
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . $this->getChildHtml();
    }
}