<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Pattern\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Pattern\Search
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RejectingPatternRepositoryInterface $patternRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RejectingPatternRepositoryInterface $patternRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->patternRepository = $patternRepository;
    }

    /**
     * Get patterns
     *
     * @return RejectingPatternInterface[]
     * @throws LocalizedException
     */
    public function searchPatterns()
    {
        $ruleList = $this->patternRepository
            ->getList($this->buildSearchCriteria())
            ->getItems();

        return $ruleList;
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add is active filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->searchCriteriaBuilder->addFilter(RejectingPatternInterface::IS_ACTIVE, 1);
        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
