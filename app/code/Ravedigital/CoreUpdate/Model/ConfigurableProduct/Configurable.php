<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ravedigital\CoreUpdate\Model\ConfigurableProduct;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\File\UploaderFactory;

class Configurable extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable
{
    /**
     * Product type code
     */
    const TYPE_CODE = 'configurable';

    /**
     * Cache key for Used Product Attribute Ids
     *
     * @var string
     * @since 100.1.0
     */
    protected $usedProductAttributeIds = '_cache_instance_used_product_attribute_ids';

    /**
     * Cache key for Used Product Attributes
     *
     * @var string
     * @since 100.1.0
     */
    protected $usedProductAttributes = '_cache_instance_used_product_attributes';

    /**
     * Cache key for Used Attributes
     *
     * @var string
     */
    protected $_usedAttributes = '_cache_instance_used_attributes';

    /**
     * Cache key for configurable attributes
     *
     * @var string
     */
    protected $_configurableAttributes = '_cache_instance_configurable_attributes';

    /**
     * Cache key for Used product ids
     *
     * @var string
     */
    protected $_usedProductIds = '_cache_instance_product_ids';

    /**
     * Cache key for used products
     *
     * @var string
     */
    protected $_usedProducts = '_cache_instance_products';

    /**
     * Cache key for salable used products
     *
     * @var string
     */
    private $usedSalableProducts = '_cache_instance_salable_products';

    /**
     * Product is composite
     *
     * @var bool
     */
    protected $_isComposite = true;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * Local cache
     *
     * @var array
     * @since 100.4.0
     */
    protected $isSaleableBySku = [];

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Catalog product type configurable
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $_catalogProductTypeConfigurable;

    /**
     * Attribute collection factory
     *
     * @var
     * \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Product collection factory
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Configurable attribute factory
     *
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory
     * @since 100.1.0
     */
    protected $configurableAttributeFactory;

    /**
     * Eav attribute factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $_eavAttributeFactory;

    /**
     * Type configurable factory
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory
     * @since 100.1.0
     */
    protected $typeConfigurableFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    private $cache;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var GalleryReadHandler
     */
    private $productGalleryReadHandler;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Product factory
     *
     * @var ProductInterfaceFactory
     */
    private $productFactory;

    /**
     * Collection salable processor
     *
     * @var SalableProcessor
     */
    private $salableProcessor;

    /**
     * @var ProductAttributeRepositoryInterface|null
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder|null
     */
    private $searchCriteriaBuilder;

   /**
    * Prepare collection for retrieving sub-products of specified configurable product
    * Retrieve related products collection with additional configuration
    *
    * @param \Magento\Catalog\Model\Product $product
    * @param bool $skipStockFilter
    * @param array $requiredAttributeIds Attributes to include in the select
    * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    private function getConfiguredUsedProductCollection(
        \Magento\Catalog\Model\Product $product,
        $skipStockFilter = true,
        $requiredAttributeIds = null
    ) {
        $collection = $this->getUsedProductCollection($product);

        if ($skipStockFilter) {
            $collection->setFlag('has_stock_status_filter', true);
        }

        $attributesForSelect = $this->getAttributesForCollection($product);
        if ($requiredAttributeIds) {
            $this->searchCriteriaBuilder->addFilter('attribute_id', $requiredAttributeIds, 'in');
            $requiredAttributes = $this->productAttributeRepository
                ->getList($this->searchCriteriaBuilder->create())->getItems();
            $requiredAttributeCodes = [];
            foreach ($requiredAttributes as $requiredAttribute) {
                $requiredAttributeCodes[] = $requiredAttribute->getAttributeCode();
            }
            $attributesForSelect = array_unique(array_merge($attributesForSelect, $requiredAttributeCodes));
        }
        $collection
            ->addAttributeToSelect($attributesForSelect)
            ->addFilterByRequiredOptions()
            ->setStoreId($product->getStoreId());

        $collection->addMediaGalleryData();
        $collection->addTierPriceData();

        return $collection;
    }
}
