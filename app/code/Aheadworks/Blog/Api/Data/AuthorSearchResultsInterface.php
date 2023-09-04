<?php
namespace Aheadworks\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface AuthorSearchResultsInterface
 * @package Aheadworks\Blog\Api\Data
 */
interface AuthorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get authors list
     *
     * @return \Aheadworks\Blog\Api\Data\AuthorInterface[]
     */
    public function getItems();

    /**
     * Set authors list
     *
     * @param \Aheadworks\Blog\Api\Data\AuthorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
