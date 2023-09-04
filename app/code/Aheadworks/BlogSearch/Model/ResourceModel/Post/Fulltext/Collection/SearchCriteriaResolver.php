<?php
namespace Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection;

use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteria;

/**
 * Class SearchCriteriaResolver
 */
class SearchCriteriaResolver
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $builder;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * SearchCriteriaResolver constructor.
     * @param SearchCriteriaBuilder $builder
     * @param string $searchRequestName
     */
    public function __construct(
        SearchCriteriaBuilder $builder,
        string $searchRequestName
    ) {
        $this->builder = $builder;
        $this->searchRequestName = $searchRequestName;
    }

    /**
     * Resolve specific attributes for search criteria
     *
     * @return SearchCriteria
     */
    public function resolve() : SearchCriteria
    {
        $searchCriteria = $this->builder->create();
        $searchCriteria->setRequestName($this->searchRequestName);
        $searchCriteria->setSortOrders(null);

        return $searchCriteria;
    }
}
