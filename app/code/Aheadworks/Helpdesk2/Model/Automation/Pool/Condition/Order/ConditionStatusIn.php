<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Pool\Condition\Order;

use Aheadworks\Helpdesk2\Api\ValidateConditionInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class ConditionStatusIn
 * @package Aheadworks\Helpdesk2\Model\Automation\Pool\Condition\Order
 */
class ConditionStatusIn implements ValidateConditionInterface
{
    /**
     * @param EventDataInterface $eventData
     * @param array $condition
     * @return bool
     */
    public function isValid(EventDataInterface $eventData, array $condition)
    {
        /** @var OrderInterface $order */
        $order = $eventData->getOrder();
        $result = false;
        if (in_array($order->getStatus(), $condition['value'])) {
            $result = true;
        }
        return $result;
    }
}
