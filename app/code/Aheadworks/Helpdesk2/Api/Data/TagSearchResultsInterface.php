<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for tag search results
 * @api
 */
interface TagSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get tags list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TagInterface[]
     */
    public function getItems();

    /**
     * Set tags list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
