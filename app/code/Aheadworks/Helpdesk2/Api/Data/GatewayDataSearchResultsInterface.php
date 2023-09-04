<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface GatewayDataSearchResultsInterface
 * @api
 */
interface GatewayDataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get gateway list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface[]
     */
    public function getItems();

    /**
     * Set gateway list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
