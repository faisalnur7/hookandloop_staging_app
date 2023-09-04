<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Automation\Pool;

use Aheadworks\Helpdesk2\Api\ValidateConditionInterface;

class ValidateConditionPool
{
    /**
     * @var array
     */
    private $conditions;

    /**
     * ConditionPool constructor.
     * @param $conditions
     */
    public function __construct(array $conditions = [])
    {
        $this->conditions = $conditions;
    }

    /**
     * @param string $object
     * @param string $operation
     * @return mixed
     */
    public function getConditionValidationObjectAndOperator(string $object, string $operation)
    {
        if (!isset($this->conditions[$object][$operation])) {
            throw new \InvalidArgumentException($object .' '.$operation. ' is unknown type');
        }
        $condition = $this->conditions[$object][$operation];
        if(!$condition instanceof ValidateConditionInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Action does not implement required interface: %s.',
                ValidateConditionInterface::class
            ));
        }
        return $this->conditions[$object][$operation];
    }
}
