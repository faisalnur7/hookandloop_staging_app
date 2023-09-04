<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerRating
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class CustomerRating implements OptionSourceInterface
{
    /**#@+
     * Constants defined for rating values calculation
     */
    const STARS_COUNT = 5;
    const MAX_VALUE = 5;
    const MIN_VALUE = 1;
    /**#@-*/

    /**
     * @var array
     */
    private $options;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            for($i = 1; $i <= self::STARS_COUNT; $i++) {
                if ($i > 1) {
                    $label = __('%1 stars', $i);
                } else {
                    $label = __('%1 star', $i);
                }
                $this->options[] = [
                    'value' => "$i",
                    'label' => $label
                ];
            }
        }

        return $this->options;
    }

    /**
     * Return array of options as value-label pairs and add rating none
     *
     * @return array
     */
    public function toOptionArrayAddNone()
    {
        $options = $this->toOptionArray();
        $options = array_merge(
            $options,
            [
                [
                    'value' => "0",
                    'label' => __('None')
                ]
            ]
        );
        return $options;
    }
}
