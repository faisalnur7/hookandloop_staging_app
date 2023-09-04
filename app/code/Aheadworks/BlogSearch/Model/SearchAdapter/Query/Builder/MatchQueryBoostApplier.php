<?php
namespace Aheadworks\BlogSearch\Model\SearchAdapter\Query\Builder;

use Aheadworks\BlogSearch\Model\Search\Request\Query\MatchFactory as MatchQueryFactory;
use Aheadworks\BlogSearch\Model\SearchAdapter\BoostResolver;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

/**
 * Class MatchQueryBoostApplier
 */
class MatchQueryBoostApplier
{
    /**
     * @var BoostResolver
     */
    private $boostResolver;

    /**
     * @var MatchQueryFactory
     */
    private $matchQueryFactory;

    /**
     * MatchPlugin constructor.
     * @param BoostResolver $boostResolver
     * @param MatchQueryFactory $matchQueryFactory
     */
    public function __construct(
        BoostResolver $boostResolver,
        MatchQueryFactory $matchQueryFactory
    ) {
        $this->boostResolver = $boostResolver;
        $this->matchQueryFactory = $matchQueryFactory;
    }

    /**
     * Applies boost to match query
     *
     * @param RequestQueryInterface $query
     * @return RequestQueryInterface
     */
    public function apply(RequestQueryInterface $query)
    {
        if ($query->getType() == RequestQueryInterface::TYPE_MATCH
           && in_array($query->getName(), $this->boostResolver->getBoostedQueryNames())) {
            $query = $this->matchQueryFactory->create([
               'name' => $query->getName(),
               'value' => $query->getValue(),
               'boost' => $this->boostResolver->getBoostByQueryName($query->getName()),
               'matches' => $query->getMatches()
           ]);
        }

        return $query;
    }
}
