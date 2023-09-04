<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

/**
 * Class CustomerGroup
 */
class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * CustomerGroup constructor.
     *
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct(private CollectionFactory $groupCollectionFactory)
    {
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = $this->groupCollectionFactory->create()->toOptionArray();
        }
        return $this->options;
    }
}
