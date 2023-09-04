<?php
namespace Aheadworks\Helpdesk2\Api;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface ValidateCondition
 * @package Aheadworks\Helpdesk2\Api
 */
interface ValidateAutomationInterface
{
    /**
     * Validate automation
     *
     * @param AutomationInterface $automation
     * @param EventDataInterface $eventData
     * @return bool
     */
    public function isValid(AutomationInterface $automation, EventDataInterface $eventData);
}
