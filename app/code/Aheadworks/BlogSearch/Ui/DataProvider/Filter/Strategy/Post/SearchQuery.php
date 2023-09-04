<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\Filter\Strategy\Post;

use Aheadworks\BlogSearch\Model\ResourceModel\Post\Fulltext\Collection as PostFulltextCollection;
use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class SearchQuery
 */
class SearchQuery implements AddFilterToCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        if (($collection instanceof PostFulltextCollection)
            && isset($condition['eq'])
        ) {
            $collection->addSearchFilter($condition['eq']);
        }
    }
}
