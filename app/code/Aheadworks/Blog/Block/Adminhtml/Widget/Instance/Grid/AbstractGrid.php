<?php
namespace Aheadworks\Blog\Block\Adminhtml\Widget\Instance\Grid;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Data\Collection;

/**
 * Class AbstractGrid
 */
abstract class AbstractGrid extends Extended
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * AbstractGrid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Collection $collection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Collection $collection,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collection = $collection;
    }

    /**
     * Prepare collection
     *
     * @return AbstractGrid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->collection);

        return parent::_prepareCollection();
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === $this->getIndexColumn()) {
            $selected = $this->getSelectedNodes();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter($this->getIdentifier(), ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter($this->getIdentifier(), ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        return "function (grid, element) {
                $(grid.containerId).fire('item:changed', {element: element});
            }";
    }

    public function getRowClickCallback()
    {
        return '';
    }

    /**
     * Adds additional parameter to URL for loading only entity grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'aw_blog_admin/widget/display',
            [
                '_current' => true,
                'uniq_id' => $this->getId(),
                'code' => $this->getTypeCode(),
                'display_type' => $this->getDisplayType()
            ]
        );
    }

    /**
     * Retrieve Type Code
     *
     * @return string
     */
    abstract protected function getTypeCode();

    /**
     * Retrieve Identifier
     *
     * @return string
     */
    abstract protected function getIdentifier();

    /**
     * Retrieve Index Column
     *
     * @return string
     */
    abstract protected function getIndexColumn();
}