<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer\Filter;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;
use Magento\Catalog\Model\Product\Visibility as SourceVisibility;

/**
 * Class Visibility
 */
class Visibility implements PreparerInterface
{
    /**
     * @var SourceVisibility
     */
    private $sourceVisibility;

    /**
     * Visibility constructor.
     * @param SourceVisibility $sourceVisibility
     */
    public function __construct(
        SourceVisibility $sourceVisibility
    ) {
        $this->sourceVisibility = $sourceVisibility;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($collection, $parameterList)
    {
        $isFilterVisibilityEnabled = $parameterList[PreparerInterface::IS_FILTER_VISIBILITY_ENABLED] ?? null;
        if ($isFilterVisibilityEnabled) {
            $collection->setVisibility($this->sourceVisibility->getVisibleInSiteIds());
        }

        return $collection;
    }
}
