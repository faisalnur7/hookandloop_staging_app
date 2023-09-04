<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model\Catalog;

/**
 * Repository for categories.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepository extends \Magento\Catalog\Model\CategoryRepository
{

    /**
     * @inheritdoc
     */
    public function get($categoryId, $storeId = null)
    {
        $cacheKey = $storeId ?? 'all';
        if (!isset($this->instances[$categoryId][$cacheKey])) {
            /** @var Category $category */
            $category = $this->categoryFactory->create();
            if (null !== $storeId) {
                $category->setStoreId($storeId);
            }
            $category->load($categoryId);
            if (!$category->getId()) {
                //throw NoSuchEntityException::singleField('id', $categoryId);
            }
            $this->instances[$categoryId][$cacheKey] = $category;
        }
        return $this->instances[$categoryId][$cacheKey];
    }
}
