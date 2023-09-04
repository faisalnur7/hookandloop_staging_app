<?php
declare(strict_types=1);
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Source\Category\Status;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Blog\Api\CategoryManagementInterface as BlogCategoryManagementInterface;
use Aheadworks\Blog\Model\Image\Info as ImageInfo;

/**
 * Class Categories
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class Categories implements DataProviderInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var BlogCategoryManagementInterface
     */
    private $blogCategoryManager;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchResultFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param BlogCategoryManagementInterface $blogCategoryManager
     * @param ImageInfo $imageInfo
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchResultFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        BlogCategoryManagementInterface $blogCategoryManager,
        ImageInfo $imageInfo
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->blogCategoryManager = $blogCategoryManager;
        $this->imageInfo = $imageInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult
    {
        $categoriesArray = [];
        $categories = $this->categoryRepository->getList($searchCriteria);

        foreach ($categories->getItems() as $category) {
            $categoryArray = $this->dataObjectProcessor
                ->buildOutputDataArray($category, CategoryInterface::class);

            $categoryArray[CategoryInterface::IMAGE_FILE_NAME] = $categoryArray[CategoryInterface::IMAGE_FILE_NAME]
                ? $this->imageInfo->getFilePath($categoryArray[CategoryInterface::IMAGE_FILE_NAME])
                : null;

            $categoryArray['children']['items'] = $this->getCategoryTree($category->getId(), $storeId);
            $categoryArray['parent'] = $this->getParentCategoryArray($category);

            $categoriesArray[] = $categoryArray;
        }

        return $this->searchResultFactory->create($categories->getTotalCount(), $categoriesArray);
    }

    /**
     * Build category tree
     *
     * @param int $categoryId
     * @param int $storeId
     * @return array
     */
    private function getCategoryTree($categoryId, $storeId)
    {
        $childCategoriesArray = [];
        $childCategories = $this->blogCategoryManager->getChildCategories(
            $categoryId,
            $storeId,
            Status::ENABLED
        );

        foreach ($childCategories as $childCategory) {
            $childCategoryArray = $this->dataObjectProcessor
                ->buildOutputDataArray($childCategory, CategoryInterface::class);
            $childCategoryArray['children']['items'] = $this->getCategoryTree($childCategory->getId(), $storeId);
            $childCategoryArray['parent'] = $this->getParentCategoryArray($childCategory);

            $childCategoriesArray[] = $childCategoryArray;
        }

        return $childCategoriesArray;
    }

    /**
     * Return parent category array
     *
     * @param CategoryInterface $category
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getParentCategoryArray($category)
    {
        $result = null;
        $parentId = $category->getParentId();

        if ($parentId && $parentId != CategoryInterface::ROOT_CATEGORY_ID) {
            /** @var CategoryInterface $parentCategory */
            $parentCategory = $this->categoryRepository->get($parentId);
            $parentCategoryArray = $this->dataObjectProcessor
                ->buildOutputDataArray($parentCategory, CategoryInterface::class);
            $result = $parentCategoryArray;
        }

        return $result;
    }
}
