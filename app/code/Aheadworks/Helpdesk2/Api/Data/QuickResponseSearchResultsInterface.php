<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QuickResponseSearchResultsInterface
 * @api
 */
interface QuickResponseSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get quick response list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface[]
     */
    public function getItems();

    /**
     * Set quick response list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
