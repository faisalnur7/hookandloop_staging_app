<?php
namespace Aheadworks\Blog\Model\Service;

use Aheadworks\Blog\Api\PostManagementInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class PostService
 * @package Aheadworks\Blog\Model\Service
 */
class PostService implements PostManagementInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PostRepositoryInterface $postRepository
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostRepositoryInterface $postRepository,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->postRepository = $postRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getPrevPost($post, $storeId, $customerGroupId)
    {
        $result = null;

        if ($post->getPublishDate()) {
            $this->searchCriteriaBuilder
                ->addFilter(PostInterface::PUBLISH_DATE, $post->getPublishDate(), 'lteq')
                ->addFilter(PostInterface::ID, $post->getId(), 'neq')
                ->addFilter(PostInterface::STATUS, Status::PUBLICATION)
                ->addFilter(PostInterface::STORE_IDS, $storeId)
                ->addFilter(PostInterface::CUSTOMER_GROUPS, $customerGroupId)
                ->setPageSize(1);

            $publishDateOrder = $this->sortOrderBuilder
                ->setField(PostInterface::PUBLISH_DATE)
                ->setDescendingDirection()
                ->create();
            $this->searchCriteriaBuilder->addSortOrder($publishDateOrder);
            $result = $this->postRepository->getList($this->searchCriteriaBuilder->create())->getItems();

            $result = reset($result);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getNextPost($post, $storeId, $customerGroupId)
    {
        $result = null;

        if ($post->getPublishDate()) {
            $this->searchCriteriaBuilder
                ->addFilter(PostInterface::PUBLISH_DATE, $post->getPublishDate(), 'gteq')
                ->addFilter(PostInterface::ID, $post->getId(), 'neq')
                ->addFilter(PostInterface::STATUS, Status::PUBLICATION)
                ->addFilter(PostInterface::STORE_IDS, $storeId)
                ->addFilter(PostInterface::CUSTOMER_GROUPS, $customerGroupId);

            $publishDateOrder = $this->sortOrderBuilder
                ->setField(PostInterface::PUBLISH_DATE)
                ->setDescendingDirection()
                ->create();
            $this->searchCriteriaBuilder->addSortOrder($publishDateOrder);
            $result = $this->postRepository->getList($this->searchCriteriaBuilder->create())->getItems();

            $result = end($result);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getRelatedPosts($post, $storeId, $customerGroupId)
    {
        $postWithRP = $this->postRepository->getWithRelatedPosts($post->getId(), $storeId, $customerGroupId);

        $relatedPosts = [];
        if ($postWithRP->getRelatedPostIds()) {
            $this->searchCriteriaBuilder->addFilter(PostInterface::ID, $postWithRP->getRelatedPostIds(), 'in');
            $relatedPosts = $this->postRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        }

        return $relatedPosts;
    }
}
