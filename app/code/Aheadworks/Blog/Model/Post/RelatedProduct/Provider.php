<?php
namespace Aheadworks\Blog\Model\Post\RelatedProduct;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatusSourceModel;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;

class Provider
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductVisibility
     */
    private $productVisibility;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductVisibility $productVisibility
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductVisibility $productVisibility,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->productRepository = $productRepository;
        $this->productVisibility = $productVisibility;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * Retrieve the list of products, related to the blog post and available within separate store
     *
     * @param PostInterface $post
     * @param int $storeId
     * @return ProductInterface[]
     */
    public function getAvailableProductList($post, $storeId)
    {
        $productIdList = is_array($post->getRelatedProductIds())
            ? $post->getRelatedProductIds()
            : [];
        $productNumber = $this->config->getRelatedProductsLimit($storeId);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'entity_id',
                $productIdList,
                'in'
            )->addFilter(
                'store_id',
                $storeId
            )->addFilter(
                ProductInterface::STATUS,
                ProductStatusSourceModel::STATUS_ENABLED
            )->addFilter(
                ProductInterface::VISIBILITY,
                $this->productVisibility->getVisibleInCatalogIds(),
                'in'
            )->setPageSize(
                $productNumber
            )->setCurrentPage(
                1
            )->create()
        ;

        return $this->productRepository->getList($searchCriteria)->getItems();
    }
}
