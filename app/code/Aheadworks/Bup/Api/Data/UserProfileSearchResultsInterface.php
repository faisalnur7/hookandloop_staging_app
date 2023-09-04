<?php
namespace Aheadworks\Bup\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface UserProfileSearchResultsInterface
 * @api
 */
interface UserProfileSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get user profile list
     *
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface[]
     */
    public function getItems();

    /**
     * Set user profile list
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
