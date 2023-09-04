<?php
namespace Aheadworks\BlogSearch\Model\Modifier\Query\Search;

use Magento\Framework\Search\RequestInterface;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    /**
     * Modifies search query
     *
     * @param array $searchQuery
     * @param RequestInterface $request
     * @return array
     */
    public function modify(array $searchQuery, RequestInterface $request);
}
