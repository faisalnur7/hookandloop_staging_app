<?php
namespace Aheadworks\Helpdesk2\Model\Source\RejectingPattern;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\CollectionFactory;

/**
 * Class PatternList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\RejectingPattern
 */
class PatternList implements OptionSourceInterface
{
    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param DataObjectConverter $dataObjectConverter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        DataObjectConverter $dataObjectConverter,
        CollectionFactory $collectionFactory
    ) {
        $this->dataObjectConverter = $dataObjectConverter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();
            $options = $this->dataObjectConverter->toOptionArray(
                $collection->getItems(),
                RejectingPatternInterface::ID,
                RejectingPatternInterface::TITLE
            );

            $this->options = $options;
        }

        return $this->options;
    }
}
