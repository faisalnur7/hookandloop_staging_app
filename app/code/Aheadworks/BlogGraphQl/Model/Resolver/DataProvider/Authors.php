<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\DataProvider;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\AuthorRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Blog\Model\Image\Info as ImageInfo;

/**
 * Class Authors
 * @package Aheadworks\BlogGraphQl\Model\Resolver\DataProvider
 */
class Authors implements DataProviderInterface
{
    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @param AuthorRepositoryInterface $authorRepository
     * @param SearchResultFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ImageInfo $imageInfo
     */
    public function __construct(
        AuthorRepositoryInterface $authorRepository,
        SearchResultFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        ImageInfo $imageInfo
    ) {
        $this->authorRepository = $authorRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->imageInfo = $imageInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId): SearchResult
    {
        $authorsArray = [];
        $authors = $this->authorRepository->getList($searchCriteria);
        foreach ($authors->getItems() as $author) {
            $authorArray = $this->dataObjectProcessor->buildOutputDataArray($author, AuthorInterface::class);
            $authorArray[AuthorInterface::IMAGE_FILE] = $authorArray[AuthorInterface::IMAGE_FILE]
                ? $this->imageInfo->getFilePath($authorArray[AuthorInterface::IMAGE_FILE])
                : null;
            $authorsArray[] = $authorArray;
        }

        return $this->searchResultFactory->create($authors->getTotalCount(), $authorsArray);
    }
}
