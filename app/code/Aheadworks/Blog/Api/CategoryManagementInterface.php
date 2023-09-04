<?php
namespace Aheadworks\Blog\Api;

/**
 * Category management service interface
 * @api
 */
interface CategoryManagementInterface
{
    /**
     * Retrieve category child categories
     *
     * @param int $categoryId
     * @param int $storeId
     * @param int|array $status
     * @return \Aheadworks\Blog\Api\Data\CategoryInterface[]|null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildCategories($categoryId, $storeId, $status);
}
