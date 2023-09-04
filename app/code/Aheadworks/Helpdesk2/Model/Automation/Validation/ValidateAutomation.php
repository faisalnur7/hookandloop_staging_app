<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Validation;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\ValidateAutomationInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\Pool\ValidateConditionPool;

/**
 * Class ValidateAutomation
 * @package Aheadworks\Helpdesk2\Model\Automation\Event\Validation
 */
class ValidateAutomation implements ValidateAutomationInterface {

    /**
     * @var ValidateConditionPool
     */
    private $validateConditionPool;

    /**
     * ValidateAutomation constructor.
     * @param ValidateConditionPool $validateConditionPool
     */
    public function __construct(
        ValidateConditionPool $validateConditionPool
    ) {
        $this->validateConditionPool = $validateConditionPool;
    }

    /**
     * @param AutomationInterface $automation
     * @param EventDataInterface $eventData
     * @return bool
     */
    public function isValid(AutomationInterface $automation, EventDataInterface $eventData)
    {
        $conditions = $automation->getConditions();
        $result = !empty($conditions);
        foreach ($conditions as $condition) {
            $conditionValidation = $this->validateConditionPool->getConditionValidationObjectAndOperator($condition['object'], $condition['operator']);
            $result = $conditionValidation->isValid($eventData, $condition) && $result;
        }
        return $result;
    }
}
