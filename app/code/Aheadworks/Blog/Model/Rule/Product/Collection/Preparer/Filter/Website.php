<?php
namespace Aheadworks\Blog\Model\Rule\Product\Collection\Preparer\Filter;

use Aheadworks\Blog\Model\Rule\Product\Collection\PreparerInterface;

class Website implements PreparerInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare($collection, $parameterList)
    {
        $websiteIds = $parameterList[PreparerInterface::WEBSITE_IDS_KEY] ?? null;
        if ($websiteIds) {
            $collection->addWebsiteFilter($websiteIds);
        }
        return $collection;
    }
}
