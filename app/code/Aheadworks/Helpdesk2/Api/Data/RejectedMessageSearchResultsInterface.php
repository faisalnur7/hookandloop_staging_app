<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RejectedMessageSearchResultsInterface
 * @api
 */
interface RejectedMessageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ticket list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface[]
     */
    public function getItems();

    /**
     * Set ticket list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
