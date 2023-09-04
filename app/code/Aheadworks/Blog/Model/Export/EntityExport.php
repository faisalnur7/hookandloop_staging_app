<?php
namespace Aheadworks\Blog\Model\Export;

use Aheadworks\Blog\Model\Export\Collection\FilterApplier;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;
use Magento\ImportExport\Model\Export\Factory;

/**
 * Class EntityExport
 */
class EntityExport extends AbstractEntity
{
    /**
     * @var string
     */
    protected $entityTypeCode;

    /**
     * @var AbstractCollection
     */
    private $collection;

    /**
     * @var AttributeCollectionProvider
     */
    private $attributeCollectionProvider;

    /**
     * @var FilterApplier
     */
    private $filterApplier;

    /**
     * EntityExport constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Factory $factory
     * @param FilterApplier $filterApplier
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param \Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory $resourceColFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Factory $factory,
        FilterApplier $filterApplier,
        AttributeCollectionProvider $attributeCollectionProvider,
        \Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory $resourceColFactory,
        $entityTypeCode = '',
        $collectionClassName = '',
        array $data = []
    ) {
        $this->filterApplier = $filterApplier;
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->collection = $factory->create($collectionClassName);
        $this->entityTypeCode = $entityTypeCode;
        parent::__construct($scopeConfig, $storeManager, $factory, $resourceColFactory, $data);
    }

    /**
     * @inheritDoc
     */
    public function export()
    {
        $writer = $this->getWriter();

        $collection = $this->filterApplier->apply(
            $this->_getEntityCollection(),
            $this->getAttributeCollection(),
            $this->_parameters
        );

        foreach ($collection->getData() as $data) {
            $writer->writeRow($data);
        }

        return $writer->getContents();
    }

    /**
     * @inheritDoc
     */
    public function exportItem($item)
    {
        // empty method
    }

    /**
     * @inheritDoc
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * @inheritDoc
     */
    protected function _getHeaderColumns()
    {
        // No need to set columns, we export all data
    }

    /**
     * @inheritDoc
     */
    protected function _getEntityCollection()
    {
        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollectionProvider->get();
    }
}