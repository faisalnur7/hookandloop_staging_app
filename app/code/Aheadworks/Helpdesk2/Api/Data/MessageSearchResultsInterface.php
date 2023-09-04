<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MessageSearchResultsInterface
 * @api
 */
interface MessageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Message list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageInterface[]
     */
    public function getItems();

    /**
     * Set Message list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
