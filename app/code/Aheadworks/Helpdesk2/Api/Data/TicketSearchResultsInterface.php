<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TicketSearchResultsInterface
 * @api
 */
interface TicketSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set ticket list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
