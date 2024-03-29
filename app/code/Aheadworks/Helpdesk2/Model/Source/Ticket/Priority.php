<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Priority\Provider as PriorityProvider;

/**
 * Class Priority
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class Priority implements OptionSourceInterface
{
    /**
     * Ticket priority values
     */
    const IF_TIME = 1;
    const TO_DO = 2;
    const ASAP = 3;
    const URGENT = 4;

    /**
     * @var PriorityProvider
     */
    private $priorityProvider;

    /**
     * @var array
     */
    private $options;

    /**
     * @param PriorityProvider $priorityProvider
     */
    public function __construct(
        PriorityProvider $priorityProvider
    ) {
        $this->priorityProvider = $priorityProvider;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = $this->priorityProvider->getListAsOptions();
        }

        return $this->options;
    }

    /**
     * Get option by option id
     *
     * @param $optionId
     * @return array|null
     * @throws LocalizedException
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
