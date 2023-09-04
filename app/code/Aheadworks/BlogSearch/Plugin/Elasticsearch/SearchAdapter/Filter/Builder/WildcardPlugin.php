<?php
namespace Aheadworks\BlogSearch\Plugin\Elasticsearch\SearchAdapter\Filter\Builder;

use Magento\Elasticsearch\SearchAdapter\Filter\Builder\Wildcard;
use Magento\Framework\Search\Request\Filter\Wildcard as WildcardFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use Aheadworks\BlogSearch\Model\SearchAdapter\BoostResolver;
use Aheadworks\BlogSearch\Model\SearchAdapter\Filter\Builder\WildcardWithBoost;

/**
 * Class WildcardPlugin
 */
class WildcardPlugin
{
    /**
     * @var WildcardWithBoost
     */
    private $wildcardWithBoost;

    /**
     * @var BoostResolver
     */
    private $boostResolver;

    /**
     * WildcardPlugin constructor.
     * @param WildcardWithBoost $wildcardWithBoost
     * @param BoostResolver $boostResolver
     */
    public function __construct(
        WildcardWithBoost $wildcardWithBoost,
        BoostResolver $boostResolver
    ) {
        $this->wildcardWithBoost = $wildcardWithBoost;
        $this->boostResolver = $boostResolver;
    }

    /**
     * Process wildcard filter boosting
     *
     * @param Wildcard $subject
     * @param array $result
     * @param RequestFilterInterface|WildcardFilterRequest $filter
     */
    public function aroundBuildFilter(Wildcard $subject, callable $proceed, RequestFilterInterface $filter)
    {
        if (in_array($filter->getName(), $this->boostResolver->getBoostedQueryNames())) {
            $result = $this->wildcardWithBoost->buildFilter($filter);
        } else {
            $result = $proceed($filter);
        }

        return $result;
    }
}
