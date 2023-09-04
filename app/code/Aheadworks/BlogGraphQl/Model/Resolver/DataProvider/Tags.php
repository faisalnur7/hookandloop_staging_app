<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Blog\Model\Url as BlogUrl;
use Aheadworks\Blog\Model\ResourceModel\TagCloudItemRepository;

/**
 * Class Tags
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class Tags implements DataProviderInterface
{
    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var BlogUrl
     */
    private $blogUrl;

    /**
     * @var TagCloudItemRepository
     */
    private $tagCloudItemRepository;

    /**
     * @param TagRepositoryInterface $tagRepository
     * @param SearchResultFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param BlogUrl $blogUrl
     * @param TagCloudItemRepository $tagCloudItemRepository
     */
    public function __construct(
        TagRepositoryInterface $tagRepository,
        SearchResultFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        BlogUrl $blogUrl,
        TagCloudItemRepository $tagCloudItemRepository
    ) {
        $this->tagRepository = $tagRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->blogUrl = $blogUrl;
        $this->tagCloudItemRepository = $tagCloudItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult
    {
        $tagsArray = [];
        $tags = $this->tagRepository->getList($searchCriteria);
        foreach ($tags->getItems() as $tag) {
            $tagArray = $this->dataObjectProcessor->buildOutputDataArray($tag, TagInterface::class);
            $tagArray['url_key'] = $this->blogUrl->getSearchByTagUrlKey($tag);
            $tagArray['count_posts'] = $this->tagCloudItemRepository->get($tag->getId(), $storeId, [PostStatus::PUBLICATION])->getPostCount();
            $tagsArray[] = $tagArray;
        }

        return $this->searchResultFactory->create($tags->getTotalCount(), $tagsArray);
    }
}
