<?php
namespace Aheadworks\Blog\Model\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\PostInterfaceFactory;
use Aheadworks\Blog\Model\ResourceModel\PostRepository;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Provider
 * @package Aheadworks\Blog\Model\Post
 */
class Provider
{
    /**
     * @var PostInterfaceFactory
     */
    private $postFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param PostInterfaceFactory $postFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PostRepository $postRepository
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        PostInterfaceFactory $postFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostRepository $postRepository
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->postFactory = $postFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->postRepository = $postRepository;
    }

    /**
     * Get post by data
     *
     * @param array $data
     * @return PostInterface
     */
    public function getByData($data)
    {
        $post = $this->postFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $post,
            $data,
            PostInterface::class
        );

        return $post;
    }

    /**
     * @param int $storeId
     * @param int $qty
     * @return PostInterface[]|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFeaturedPosts($storeId, $qty)
    {
        $this->searchCriteriaBuilder
            ->addFilter('status', Status::PUBLICATION)
            ->addFilter(PostInterface::IS_FEATURED, true)
            ->addFilter(PostInterface::STORE_IDS, $storeId)
            ->setPageSize($qty);

        return $this->postRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * Retrieve the list of posts for reindex
     *
     * @param array $ids
     * @param array $storeIds
     * @return \Aheadworks\Blog\Api\Data\PostSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListForReindex($ids = [], $storeIds = [])
    {
        $this->searchCriteriaBuilder
            ->addFilter('status', Status::PUBLICATION)
            ->addFilter(PostInterface::ARE_RELATED_PRODUCTS_ENABLED, true)
        ;

        if ($ids) {
            $this->searchCriteriaBuilder->addFilter('id', $ids, 'in');
        }

        if ($storeIds) {
            $this->searchCriteriaBuilder->addFilter(PostInterface::STORE_IDS, $storeIds, 'in');
        }

        return $this->postRepository->getList($this->searchCriteriaBuilder->create());
    }
}
