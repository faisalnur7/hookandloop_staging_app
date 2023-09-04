<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection as Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\CollectionFactory as CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class DepartmentList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class DepartmentList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->setStoreId($this->storeManager->getStore()->getId() ?? Store::DEFAULT_STORE_ID);
            $collection->addFilter(DepartmentInterface::IS_ACTIVE, 1);
            $collection->setOrder(DepartmentInterface::SORT_ORDER, Collection::SORT_ORDER_ASC);

            foreach ($collection as $item) {
                $item->setName($item->getCurrentStorefrontLabel()['content'] ?? $item->getName());
            }
            $this->options = $collection->toOptionArray();
        }

        return $this->options;
    }

    /**
     * Get option by option id
     *
     * @param int $optionId
     * @return array|null
     */
    public function getOptionById($optionId)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $optionId) {
                return $option;
            }
        }

        return null;
    }
}
