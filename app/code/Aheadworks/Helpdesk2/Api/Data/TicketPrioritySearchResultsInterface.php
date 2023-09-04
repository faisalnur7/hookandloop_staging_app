<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TicketPrioritySearchResultsInterface
 * @api
 */
interface TicketPrioritySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket priority list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface[]
     */
    public function getItems();

    /**
     * Set ticket priority list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
