<?php
namespace Aheadworks\Bup\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface UserProfileMetadataSearchResultsInterface
 * @api
 */
interface UserProfileMetadataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get user profile metadata list
     *
     * @return \Aheadworks\Bup\Api\Data\UserProfileMetadataInterface[]
     */
    public function getItems();

    /**
     * Set user profile metadata list
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileMetadataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
