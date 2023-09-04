<?php
namespace Aheadworks\Helpdesk2\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class Group
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $customerGroupCollection;

    /**
     * @param Collection $customerGroupCollection
     */
    public function __construct(
        Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
