<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface ListDataProviderInterface
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
interface DataProviderInterface
{
    /**
     * Retrieve data
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return SearchResult
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult;
}
