<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface RejectingPatternSearchResultsInterface
 *
 * @package Aheadworks\Helpdesk2\Api\Data
 */
interface RejectingPatternSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pattern list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface[]
     */
    public function getItems();

    /**
     * Set pattern list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
