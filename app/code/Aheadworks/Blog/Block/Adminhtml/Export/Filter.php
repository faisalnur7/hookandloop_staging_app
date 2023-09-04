<?php
namespace Aheadworks\Blog\Block\Adminhtml\Export;

/**
 * Class Filter
 */
class Filter extends \Magento\ImportExport\Block\Adminhtml\Export\Filter
{
    /**
     * Add columns to grid
     *
     * @return $this|Filter
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->sortColumnsByOrder();

        $this->addColumn(
            'default_frontend_label',
            [
                'header' => __('Attribute Label'),
                'index' => 'default_frontend_label',
                'sortable' => false,
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        $this->addColumn(
            'attribute_code',
            [
                'header' => __('Attribute Code'),
                'index' => 'attribute_code',
                'header_css_class' => 'col-code',
                'column_css_class' => 'col-code'
            ]
        );
        $this->addColumn(
            'filter',
            [
                'header' => __('Filter'),
                'sortable' => false,
                'filter' => false,
                'frame_callback' => [$this, 'decorateFilter']
            ]
        );

        if ($this->hasOperation()) {
            $operation = $this->getOperation();
            $skipAttr = $operation->getSkipAttr();
            if ($skipAttr) {
                $this->getColumn('skip')->setData('values', $skipAttr);
            }
            $filter = $operation->getExportFilter();
            if ($filter) {
                $this->getColumn('filter')->setData('values', $filter);
            }
        }

        return $this;
    }
}