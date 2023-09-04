<?php
namespace Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection;
use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection as PostFulltextCollection;

/**
 * Class SearchResultApplier
 */
class SearchResultApplier
{
    /**
     * @var Collection|PostFulltextCollection
     */
    private $collection;

    /**
     * @var SearchResultInterface
     */
    private $searchResult;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * SearchResultApplier constructor.
     * @param Collection $collection
     * @param SearchResultInterface $searchResult
     * @param int $size
     * @param int $currentPage
     */
    public function __construct(
        Collection $collection,
        SearchResultInterface $searchResult,
        int $size,
        int $currentPage
    ) {
        $this->collection = $collection;
        $this->searchResult = $searchResult;
        $this->size = $size;
        $this->currentPage = $currentPage;
    }

    /**
     * Applies search result to collection
     */
    public function apply()
    {
        if (empty($this->searchResult->getItems())) {
            $this->collection->getSelect()->where('NULL');

            return;
        }

        $items = $this->sliceItems($this->searchResult->getItems(), $this->size, $this->currentPage);

        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->getId();
        }

        $this->collection->getSelect()
            ->where('main_table.' . PostInterface::ID . ' IN (?)', $ids)
            ->reset(\Magento\Framework\DB\Select::ORDER);

        $idsList = implode(',', $ids);
        $this->collection->getSelect()
            ->order(new \Zend_Db_Expr("FIELD(main_table.id, $idsList)"));
    }

    /**
     * Slice current items
     *
     * @param array $items
     * @param int $size
     * @param int $currentPage
     * @return array
     */
    private function sliceItems(array $items, int $size, int $currentPage): array
    {
        if ($size !== 0) {
            // Check that current page is in a range of allowed page numbers, based on items count and items per page,
            // than calculate offset for slicing items array.
            $itemsCount = count($items);
            $maxAllowedPageNumber = ceil($itemsCount/$size);
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            if ($currentPage > $maxAllowedPageNumber) {
                $currentPage = $maxAllowedPageNumber;
            }

            $offset = $this->getOffset($currentPage, $size);
            $items = array_slice($items, $offset, $size);
        }

        return $items;
    }

    /**
     * Get offset for given page.
     *
     * @param int $pageNumber
     * @param int $pageSize
     * @return int
     */
    private function getOffset(int $pageNumber, int $pageSize): int
    {
        return ($pageNumber - 1) * $pageSize;
    }
}
