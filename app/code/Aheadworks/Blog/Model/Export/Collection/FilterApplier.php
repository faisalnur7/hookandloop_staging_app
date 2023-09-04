<?php
namespace Aheadworks\Blog\Model\Export\Collection;

use Aheadworks\Blog\Model\Export;
use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Collection as AttributeCollection;
use Aheadworks\Blog\Model\Export\Collection\Filter\ProcessorAggregator;

/**
 * Class FilterApplier
 */
class FilterApplier
{
    /**
     * @var ProcessorAggregator
     */
    private $filterProcessor;

    /**
     * FilterApplier constructor.
     * @param ProcessorAggregator $filterProcessor
     */
    public function __construct(
        ProcessorAggregator $filterProcessor
    ) {
        $this->filterProcessor = $filterProcessor;
    }

    /**
     * Apply filters to collection
     *
     * @param AbstractCollection $collection
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return AbstractCollection
     * @throws LocalizedException
     */
    public function apply(AbstractCollection $collection, AttributeCollection $attributeCollection, $filters)
    {
        foreach ($this->retrieveFilterData($filters) as $columnName => $value) {
            $attributeDefinition = $attributeCollection->getItemById($columnName);
            if (!$attributeDefinition) {
                throw new LocalizedException(__(
                    'Given column name "%columnName" is not present in collection.',
                    ['columnName' => $columnName]
                ));
            }

            $type = $attributeDefinition->getData('backend_type');
            if (!$type) {
                throw new LocalizedException(__(
                    'There is no backend type specified for column "%columnName".',
                    ['columnName' => $columnName]
                ));
            }

            $this->filterProcessor->process($collection, $columnName, $value, $type);
        }

        return $collection;
    }

    /**
     * Retrieve filter data
     *
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters)
    {
        return array_filter(
            $filters[Export::FILTER_ELEMENT_GROUP] ?? [],
            function ($value) {
                return $value !== '';
            }
        );
    }
}