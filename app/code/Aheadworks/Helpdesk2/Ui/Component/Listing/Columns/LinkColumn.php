<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class LinkColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Columns
 */
class LinkColumn extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
        $urlEntityParamName = $this->getData('config/urlEntityParamName') ?? 'id';
        $entityFieldName = $this->getData('config/entityFieldName') ?? 'id';
        $cancelingLinkValues = $this->getData('config/cancelingLinkValues') ?? [];
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName . '_label'] = $item[$fieldName];
            if (!in_array($item[$entityFieldName], $cancelingLinkValues, true)) {
                $item[$fieldName . '_url'] = $this->context->getUrl(
                    $viewUrlPath,
                    [$urlEntityParamName => $item[$entityFieldName]]
                );
            }
        }

        return $dataSource;
    }
}
