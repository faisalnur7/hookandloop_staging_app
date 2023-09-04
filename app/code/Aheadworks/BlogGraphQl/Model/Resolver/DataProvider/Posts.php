<?php
declare(strict_types=1);
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\BlogGraphQl\Model\TemplateFilter\FilterInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Blog\Model\Source\Category\Status as CategoryStatus;
use Aheadworks\Blog\Api\PostManagementInterface;

/**
 * Class Posts
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class Posts implements DataProviderInterface
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var FilterInterface
     */
    private $templateFilter;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var PostManagementInterface
     */
    private $postService;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param PostRepositoryInterface $postRepository
     * @param SearchResultFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param FilterInterface $templateFilter
     * @param CategoryRepositoryInterface $categoryRepository
     * @param TagRepositoryInterface $tagRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PostManagementInterface $postService
     * @param HttpContext $httpContext
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        SearchResultFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        FilterInterface $templateFilter,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostManagementInterface $postService,
        HttpContext $httpContext
    ) {
        $this->postRepository = $postRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->templateFilter = $templateFilter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->postService = $postService;
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult
    {
        $postsArray = [];
        $categoriesIds = [];
        $tagIds = [];
        $posts = $this->postRepository->getList($searchCriteria);

        foreach ($posts->getItems() as $post) {
            $postArray = $this->dataObjectProcessor->buildOutputDataArray($post, PostInterface::class);

            foreach ($post->getCategoryIds() as $item) {
                if (!in_array($item, $categoriesIds)) {
                    $categoriesIds[] = $item;
                }
            }

            foreach ($post->getTagIds() as $item) {
                if (!in_array($item, $tagIds)) {
                    $tagIds[] = $item;
                }
            }

            $postArray[PostInterface::SHORT_CONTENT] = $this->templateFilter->filter($post->getShortContent());
            $postArray[PostInterface::CONTENT] = $this->templateFilter->filter($post->getContent());
            $postArray[PostInterface::CUSTOMER_GROUPS] = explode(',', $post->getCustomerGroups());

            $customerGroup = $this->httpContext->getValue(Context::CONTEXT_GROUP);
            $postArray['next_post'] = $this->postService->getNextPost($post, $storeId, $customerGroup);
            $postArray['previous_post'] = $this->postService->getPrevPost($post, $storeId, $customerGroup);
            $postArray['related_posts']['items'] = $this->postService->getRelatedPosts($post, $storeId, $customerGroup);
            $postsArray[] = $postArray;
        }

        $postsArray = $this->AddCategoriesToPost($categoriesIds, $postsArray, $storeId);
        $postsArray = $this->AddTagsToPost($tagIds, $postsArray);

        return $this->searchResultFactory->create($posts->getTotalCount(), $postsArray);
    }

    /**
     * add categories to blog post
     *
     * @param array $categoriesIds
     * @param array $postsArray
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function AddCategoriesToPost($categoriesIds, $postsArray, $storeId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::STATUS, CategoryStatus::ENABLED)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->addFilter(CategoryInterface::ID, $categoriesIds, 'in')
            ->addSortOrder(
                new SortOrder(
                    [
                        SortOrder::FIELD => CategoryInterface::NAME,
                        SortOrder::DIRECTION => SortOrder::SORT_ASC
                    ]
                )
            );
        $categories = [];
        $categoriesList = $this->categoryRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($categoriesList->getItems() as $category) {
            $categories[$category->getId()] = $this->dataObjectProcessor->buildOutputDataArray($category, CategoryInterface::class);
        }

        foreach ($postsArray as $key => $post) {

            foreach ($post['category_ids'] as $value) {
                if (array_key_exists($value, $categories)) {
                    $post['categories']['items'][] = $categories[$value];
                }
            }

            $postsArray[$key] = $post;
        }

        return $postsArray;
    }

    /**
     * add tags to blog post
     *
     * @param array $tagIds
     * @param array $postsArray
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function AddTagsToPost($tagIds, $postsArray)
    {
        $this->searchCriteriaBuilder
            ->addFilter(TagInterface::ID, $tagIds, 'in')
            ->addSortOrder(
                new SortOrder(
                    [
                        SortOrder::FIELD => TagInterface::NAME,
                        SortOrder::DIRECTION => SortOrder::SORT_ASC
                    ]
                )
            );

        $tags = [];
        $tagsList = $this->tagRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($tagsList->getItems() as $tag) {
            $tags[$tag->getId()] = $this->dataObjectProcessor->buildOutputDataArray($tag, TagInterface::class);
            $tags[$tag->getId()]['url_key'] = urlencode($tag->getName());
        }

        foreach ($postsArray as $key => $post) {

            foreach ($post['tag_ids'] as $value) {
                if (array_key_exists($value, $tags)) {
                    $post['tags']['items'][] = $tags[$value];
                }
            }

            $postsArray[$key] = $post;
        }

        return $postsArray;
    }
}
