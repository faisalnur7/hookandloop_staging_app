<?php
namespace Aheadworks\BlogSearch\Model\Indexer\Post\Fulltext\Action;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\Post\Status;
use Aheadworks\Blog\Api\PostRepositoryInterface as PostRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class DataProvider
 */
class DataProvider
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * DataProvider constructor.
     * @param PostRepository $postRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        PostRepository $postRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->postRepository = $postRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Returns searchable posts
     *
     * To be suitable for search post must meet set of requirements:
     * - to be published
     * - to be attached to store
     *
     * @param int $storeId Store View Id
     * @param int[]|null $postIds
     * @return PostInterface[]
     * @throws LocalizedException
     */
    public function getSearchablePosts($storeId, $postIds = null)
    {
        if ($postIds) {
            $this->searchCriteriaBuilder->addFilter(PostInterface::ID, $postIds, 'in');
        }

        $this->searchCriteriaBuilder
            ->addFilter(PostInterface::STATUS, Status::PUBLICATION)
            ->addFilter(PostInterface::STORE_IDS, [$storeId], 'in');

        return $this->postRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }
}