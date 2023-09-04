<?php
namespace Aheadworks\BlogSearch\Model\Modifier\Query\Search\Post;

use Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext;
use Magento\Framework\Search\RequestInterface;
use Aheadworks\BlogSearch\Model\Modifier\Query\Search\ModifierInterface;

/**
 * Class Sort
 */
class Sort implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function modify(array $searchQuery, RequestInterface $request)
    {
        if ($request->getName() == Fulltext::INDEXER_ID) {
            $searchQuery['body']['sort'] = [
                [
                    '_score' => [
                        'order' => 'desc',
                    ]
                ]
            ];
        }

        return $searchQuery;
    }
}
