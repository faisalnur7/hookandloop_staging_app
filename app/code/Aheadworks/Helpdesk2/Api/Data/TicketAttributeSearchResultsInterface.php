<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TicketAttributeSearchResultsInterface
 * @api
 */
interface TicketAttributeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket attribute list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface[]
     */
    public function getItems();

    /**
     * Set ticket attribute list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
