<?php
namespace Aheadworks\Helpdesk2\Api;

use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface ValidateConditionInterface
 * @package Aheadworks\Helpdesk2\Api
 */
interface ValidateConditionInterface
{
    /**
     * Validate condition
     *
     * @param EventDataInterface $eventData
     * @param array $condition
     * @return mixed
     */
    public function isValid(EventDataInterface $eventData, array $condition);
}
