<?php
namespace Aheadworks\Helpdesk2\Model\Source\Order;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Order
 */
class Status implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
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
            $this->options = $collection->toOptionArray();
        }

        return $this->options;
    }
}
