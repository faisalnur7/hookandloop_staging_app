<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class MultiSelectColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Columns
 */
class MultiSelectColumn extends Column
{
    /**
     * @var array
     */
    private $options;

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            $content = '';
            foreach ($item[$fieldName] as $value) {
                $content .= $this->getValueLabel($value) . '</br>';
            }
            $item[$this->getData('name')] = $content;
        }

        return $dataSource;
    }

    /**
     * Get value label
     *
     * @param string|int $value
     * @return string
     */
    private function getValueLabel($value)
    {
        if ($this->options === null) {
            $options = $this->getData('options');
            $optionData = [];
            foreach ($options->toOptionArray() as $option) {
                $optionData[$option['value']] = $option['label'];
            }
            $this->options = $optionData;
        }

        return $this->options[$value] ?? '';
    }
}
