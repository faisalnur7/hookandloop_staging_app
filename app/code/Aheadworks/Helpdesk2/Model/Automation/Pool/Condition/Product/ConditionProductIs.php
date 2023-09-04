<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Pool\Condition\Product;

use Aheadworks\Helpdesk2\Api\ValidateConditionInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class ConditionProductIs
 * @package Aheadworks\Helpdesk2\Model\Automation\Pool\Condition\Product
 */
class ConditionProductIs implements ValidateConditionInterface
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
        $skus = explode(',', (string)$condition['value']);
        $countProducts = 0;
        foreach ($order->getAllItems() as $product) {
            $countProducts += (int) in_array($product->getSku(), $skus);
        }
        if ($countProducts == count($skus)) {
            $result = true;
        }
        return $result;
    }
}
