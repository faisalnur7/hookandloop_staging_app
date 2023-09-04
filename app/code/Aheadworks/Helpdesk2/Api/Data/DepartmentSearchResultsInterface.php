<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface DepartmentSearchResultsInterface
 * @api
 */
interface DepartmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get department list
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface[]
     */
    public function getItems();

    /**
     * Set department list
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
