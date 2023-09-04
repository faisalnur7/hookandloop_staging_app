<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Tags
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class Tags extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[TicketInterface::TAG_NAMES]) && !empty($item[TicketInterface::TAG_NAMES])) {
                    $item['tags'] = $item[TicketInterface::TAG_NAMES];
                    $item[TicketInterface::TAG_NAMES] = array_map(
                        'trim',
                        explode(',', (string)$item[TicketInterface::TAG_NAMES])
                    );
                }
            }
        }
        return $dataSource;
    }
}
