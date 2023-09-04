<?php
namespace Aheadworks\Blog\Model\Post\Listing;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Blog\Api\PostRepositoryInterface as PostRepository;

/**
 * Class Builder
 * @package Aheadworks\Blog\Model\Post\Listing
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * Builder constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PostRepository $postRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostRepository $postRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->postRepository = $postRepository;
    }

    /**
     * Returns posts list
     *
     * @return \Aheadworks\Blog\Api\Data\PostInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostList()
    {
        return $this->postRepository
            ->getList($this->buildSearchCriteria())
            ->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Build search criteria
     *
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
