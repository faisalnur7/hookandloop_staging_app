<?php
namespace Aheadworks\Blog\ViewModel\Category;

use Aheadworks\Blog\Model\Image\Info as ImageInfo;
use Aheadworks\Blog\Model\Source\Category\Options\DisplayModes;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Details
 *
 * @package Aheadworks\Blog\ViewModel\Category
 */
class Details implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @var FilterProvider
     */
    private $templateFilterProvider;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ImageInfo $imageInfo
     * @param FilterProvider $templateFilterProvider
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        ImageInfo $imageInfo,
        FilterProvider $templateFilterProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->imageInfo = $imageInfo;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve current category
     *
     * @return CategoryInterface|null
     */
    public function getCurrentCategory()
    {
        $categoryId = $this->request->getParam('blog_category_id');
        try {
            $currentCategory = $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            $currentCategory = null;
        }
        return $currentCategory;
    }

    /**
     * Check is static only mode
     *
     * @param CategoryInterface $category
     * @return bool
     */
    public function isStaticOnlyMode($category)
    {
        return $category->getDisplayMode() === DisplayModes::DM_STATIC_BLOCK_ONLY;
    }

    /**
     * Check is mixed mode
     *
     * @param CategoryInterface $category
     * @return bool
     */
    public function isMixedMode($category)
    {
        return $category->getDisplayMode() === DisplayModes::DM_MIXED;
    }

    /**
     * Check is blog post only mode
     *
     * @param CategoryInterface $category
     * @return bool
     */
    public function isPostOnlyMode($category)
    {
        return $category->getDisplayMode() === DisplayModes::DM_BLOG_POSTS_ONLY;
    }

    /**
     * Check if need to display details block for specific category
     *
     * @param CategoryInterface|null $category
     * @return bool
     */
    public function isNeedToDisplayDetails($category)
    {
        return $category
            && (
                $this->isNeedToDisplayImage($category)
                || $this->isNeedToDisplayDescription($category)
            );
    }

    /**
     * Check if need to display image for specific category
     *
     * @param CategoryInterface $category
     * @return bool
     */
    public function isNeedToDisplayImage($category)
    {
        return !empty($category->getImageFileName());
    }

    /**
     * Retrieve image URL for specific category
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getImageUrl($category)
    {
        try {
            $url = $this->imageInfo->getMediaUrl($category->getImageFileName());
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * Check if need to display description for specific category
     *
     * @param CategoryInterface $category
     * @return bool
     */
    public function isNeedToDisplayDescription($category)
    {
        return $category->getIsDescriptionEnabled();
    }

    /**
     * Return formatted description
     *
     * @param $category
     * @return string
     */
    public function getDescription($category)
    {
        $categoryDescription = $category->getDescription();
        $storeId = $this->storeManager->getStore()->getId();

        return $this->templateFilterProvider->getFilter()
            ->setStoreId($storeId)
            ->filter($categoryDescription);
    }
}
