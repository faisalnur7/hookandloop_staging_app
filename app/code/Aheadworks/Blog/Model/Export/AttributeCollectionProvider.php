<?php
namespace Aheadworks\Blog\Model\Export;

use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;

/**
 * Class AttributeCollectionProvider
 */
class AttributeCollectionProvider
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $attributes
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory $attributeFactory,
        DataObjectHelper $dataObjectHelper,
        $attributes = []
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
        $this->attributes = $attributes;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Retrieve attribute collection
     *
     * @return Collection
     * @throws \Exception
     */
    public function get(): Collection
    {
        if (count($this->collection) === 0) {
            foreach ($this->attributes as $attribute) {
                /** @var \Magento\Eav\Model\Entity\Attribute $sourceCodeAttribute */
                $sourceCodeAttribute = $this->attributeFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $sourceCodeAttribute,
                    $attribute,
                    \Magento\Eav\Api\Data\AttributeInterface::class
                );

                $this->collection->addItem($sourceCodeAttribute);
            }
        }

        return $this->collection;
    }
}