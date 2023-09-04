<?php
namespace Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext;

use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection\SearchCriteriaResolver;
use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection\SearchCriteriaResolverFactory;
use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection\SearchResultApplier;
use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection\SearchResultApplierFactory;
use Magento\Framework\Search\Search;

/**
 * Class Collection
 */
class Collection extends \Aheadworks\Blog\Model\ResourceModel\Post\Collection
{
    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SearchCriteriaResolverFactory
     */
    private $searchCriteriaResolverFactory;

    /**
     * @var SearchResultApplierFactory
     */
    private $searchResultApplierFactory;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var SearchResultInterface
     */
    private $searchResult;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param EventManager $eventManager
     * @param DateTime $dateTime
     * @param MetadataPool $metadataPool
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchCriteriaResolverFactory $searchCriteriaResolverFactory
     * @param SearchResultApplierFactory $searchResultApplierFactory
     * @param SearchResultFactory $searchResultFactory
     * @param Search $search
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @param string $searchRequestName
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        EventManager $eventManager,
        DateTime $dateTime,
        MetadataPool $metadataPool,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchCriteriaResolverFactory $searchCriteriaResolverFactory,
        SearchResultApplierFactory $searchResultApplierFactory,
        SearchResultFactory $searchResultFactory,
        Search $search,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        $searchRequestName = 'aheadworks_blogsearch_post_fulltext'
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $dateTime,
            $metadataPool,
            $connection,
            $resource
        );

        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchCriteriaResolverFactory = $searchCriteriaResolverFactory;
        $this->searchResultApplierFactory = $searchResultApplierFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->search = $search;
        $this->searchRequestName = $searchRequestName;
    }

    /**
     * Add search query filter
     *
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        if ($this->isLoaded()) {
            return;
        }

        $this->queryText = $this->queryText ?? '';
        if (strlen(trim($this->queryText))) {
            $this->prepareSearchTermFilter();
            $searchCriteria = $this->getSearchCriteriaResolver()->resolve();
            try {
                /** @var SearchResultInterface $searchResult */
                $this->searchResult = $this->search->search($searchCriteria);
            } catch (\Exception $e) {
                $this->searchResult = $this->createEmptyResult();
                $this->_logger->error($e->getMessage());
            }
        } else {
            $this->searchResult = $this->createEmptyResult();
        }

        $this->getSearchResultApplier($this->searchResult)->apply();
        parent::_renderFiltersBefore();
    }

    /**
     * Prepare search term filter for text query.
     *
     * @return void
     */
    private function prepareSearchTermFilter(): void
    {
        if ($this->queryText) {
            $this->filterBuilder->setField('search_term');
            $this->filterBuilder->setValue($this->queryText);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }
    }

    /**
     * Get search criteria resolver.
     *
     * @return SearchCriteriaResolver
     */
    private function getSearchCriteriaResolver(): SearchCriteriaResolver
    {
        return $this->searchCriteriaResolverFactory->create(
            [
                'builder' => $this->searchCriteriaBuilder,
                'searchRequestName' => $this->searchRequestName,
            ]
        );
    }

    /**
     * Get search result applier.
     *
     * @param SearchResultInterface $searchResult
     * @return SearchResultApplier
     */
    private function getSearchResultApplier(SearchResultInterface $searchResult): SearchResultApplier
    {
        return $this->searchResultApplierFactory->create(
            [
                'collection' => $this,
                'searchResult' => $searchResult,
                /** This variable sets by serOrder method, but doesn't have a getter method. */
                'orders' => $this->_orders,
                'size' => $this->getPageSize(),
                'currentPage' => (int)$this->_curPage,
            ]
        );
    }

    /**
     * Create empty search result
     *
     * @return SearchResultInterface
     */
    private function createEmptyResult()
    {
        return $this->searchResultFactory->create()->setItems([]);
    }
}
