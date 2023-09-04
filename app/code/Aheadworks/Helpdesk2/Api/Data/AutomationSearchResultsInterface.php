<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface AutomationSearchResultsInterface
 * @api
 */
interface AutomationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get automation list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface[]
     */
    public function getItems();

    /**
     * Set automation list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
