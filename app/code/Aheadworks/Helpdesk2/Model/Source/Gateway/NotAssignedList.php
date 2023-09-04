<?php
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection as GatewayCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory as GatewayCollectionFactory;

/**
 * Class NotAssignedList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class NotAssignedList implements OptionSourceInterface
{
    const NOT_ASSIGNED_VALUE = '0';

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var GatewayCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param DataObjectConverter $dataObjectConverter
     * @param GatewayCollectionFactory $collectionFactory
     */
    public function __construct(
        DataObjectConverter $dataObjectConverter,
        GatewayCollectionFactory $collectionFactory
    ) {
        $this->dataObjectConverter = $dataObjectConverter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        /** @var GatewayCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->applyNotAssignedFilter();

        $options = $this->dataObjectConverter->toOptionArray(
            $collection->getItems(),
            GatewayDataInterface::ID,
            GatewayDataInterface::NAME
        );

        array_unshift(
            $options,
            [
                'value' => self::NOT_ASSIGNED_VALUE,
                'label' => __('Not Assigned')
            ]
        );

        return $options;
    }
}
