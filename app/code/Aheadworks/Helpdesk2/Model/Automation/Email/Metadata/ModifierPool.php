<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class ModifierPool
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata
 */
class ModifierPool
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(
        array $modifierList = []
    ) {
        $this->modifierList = $modifierList;
    }

    /**
     * Retrieve metadata modifier for specific event
     *
     * @param string $eventName
     * @return ModifierInterface
     */
    public function getModifierForEvent($eventName)
    {
        if (!isset($this->modifierList[$eventName])) {
            throw new \InvalidArgumentException(
                __('Unknown email metadata modifier for event action: %1', $eventName)
            );
        }

        return $this->modifierList[$eventName];
    }
}
