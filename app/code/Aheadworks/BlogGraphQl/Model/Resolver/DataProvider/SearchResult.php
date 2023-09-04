<?php
declare(strict_types=1);
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class SearchResult
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class SearchResult
{
    /**
     * @var SearchResultsInterface
     */
    private $totalCount;

    /**
     * @var array
     */
    private $items;

    /**
     * @param int $totalCount
     * @param array $items
     */
    public function __construct(int $totalCount, array $items)
    {
        $this->totalCount = $totalCount;
        $this->items = $items;
    }

    /**
     * Retrieve total count
     *
     * @return int
     */
    public function getTotalCount() : int
    {
        return $this->totalCount;
    }

    /**
     * Retrieve items as array
     *
     * @return array
     */
    public function getItems() : array
    {
        return $this->items;
    }
}
