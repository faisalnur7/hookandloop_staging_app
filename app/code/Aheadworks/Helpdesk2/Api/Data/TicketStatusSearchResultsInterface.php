<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TicketStatusSearchResultsInterface
 * @api
 */
interface TicketStatusSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket status list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface[]
     */
    public function getItems();

    /**
     * Set ticket status list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
