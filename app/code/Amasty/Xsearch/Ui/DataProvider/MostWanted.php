<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Ui\DataProvider;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as MagentoDataProvider;

class MostWanted extends MagentoDataProvider
{
    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $result = [];

        /** @var DataObject $aggregatedItem **/
        foreach ($searchResult->getItems() as $aggregatedItem) {
            $result['items'][] = $aggregatedItem->getData();
        }

        $result['totalRecords'] = $searchResult->getTotalCount();

        return $result;
    }
}
