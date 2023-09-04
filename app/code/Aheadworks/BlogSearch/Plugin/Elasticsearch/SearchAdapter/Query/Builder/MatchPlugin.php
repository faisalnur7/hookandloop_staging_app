<?php
declare(strict_types=1);

namespace Aheadworks\BlogSearch\Plugin\Elasticsearch\SearchAdapter\Query\Builder;

use Aheadworks\BlogSearch\Model\SearchAdapter\Query\Builder\MatchQueryBoostApplier;
use Magento\Elasticsearch\SearchAdapter\Query\Builder\QueryInterface;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

/**
 * Class MatchPlugin
 */
class MatchPlugin
{
    /**
     * @var MatchQueryBoostApplier
     */
    private $matchQueryBoostApplier;

    /**
     * MatchPlugin constructor.
     *
     * @param MatchQueryBoostApplier $matchQueryBoostApplier
     */
    public function __construct(
        MatchQueryBoostApplier $matchQueryBoostApplier
    ) {
        $this->matchQueryBoostApplier = $matchQueryBoostApplier;
    }

    /**
     * Adds boost to match queries
     *
     * @param  QueryInterface        $subject
     * @param  array                 $selectQuery
     * @param  RequestQueryInterface $requestQuery
     * @param  string                $conditionType
     * @return array
     */
    public function beforeBuild(
        QueryInterface $subject,
        array $selectQuery,
        RequestQueryInterface $requestQuery,
        $conditionType
    ) {
        $requestQuery = $this->matchQueryBoostApplier->apply($requestQuery);
        return [$selectQuery, $requestQuery, $conditionType];
    }
}
