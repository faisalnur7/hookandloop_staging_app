<?php
declare(strict_types=1);
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class SearchResultFactory
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class SearchResultFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create SearchResult
     *
     * @param int $totalCount
     * @param array $items
     * @return SearchResult
     */
    public function create(int $totalCount, array $items) : SearchResult
    {
        return $this->objectManager->create(
            SearchResult::class,
            ['totalCount' => $totalCount, 'items' => $items]
        );
    }
}
