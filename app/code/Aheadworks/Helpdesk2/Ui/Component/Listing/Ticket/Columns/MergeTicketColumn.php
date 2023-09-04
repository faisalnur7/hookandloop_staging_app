<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class MergeTicketColumn
 */
class MergeTicketColumn extends Column
{
    /**
     * MergeTicketColumn constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_html'] =
                    "<button class='button'><span>".__("Merge Tickets")."</span></button>";
                $item[$fieldName . '_entityId'] = $item['entity_id'];
                $item[$fieldName . '_ticket_uid'] = $item['uid'];
            }
        }
        return $dataSource;
    }
}
