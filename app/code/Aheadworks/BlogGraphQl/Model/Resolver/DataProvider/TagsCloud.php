<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Blog\Api\Data\TagCloudItemInterface;
use Aheadworks\Blog\Api\TagCloudItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class TagsCloud
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class TagsCloud implements DataProviderInterface
{
    /**
     * @var TagCloudItemRepositoryInterface
     */
    private $tagCloudItemRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param TagCloudItemRepositoryInterface $tagCloudItemRepository
     * @param SearchResultFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        TagCloudItemRepositoryInterface $tagCloudItemRepository,
        SearchResultFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->tagCloudItemRepository = $tagCloudItemRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult
    {
        $tagsArray = [];
        $tags = $this->tagCloudItemRepository->getList($searchCriteria, $storeId);
        foreach ($tags->getItems() as $tag) {
            $tagsArray[] = $this->dataObjectProcessor->buildOutputDataArray($tag, TagCloudItemInterface::class);
        }

        return $this->searchResultFactory->create($tags->getTotalCount(), $tagsArray);
    }
}
