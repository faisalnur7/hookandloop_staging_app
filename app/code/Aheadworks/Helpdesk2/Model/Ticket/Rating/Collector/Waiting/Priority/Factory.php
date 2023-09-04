<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $objectClasses;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $objectClasses
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $objectClasses
    ) {
        $this->objectManager = $objectManager;
        $this->objectClasses = $objectClasses;
    }

    /**
     * Create priority collector by ticket priority ID
     *
     * @param int $priorityId
     * @return AbstractCollector
     */
    public function createByPriority($priorityId)
    {
        if (isset($this->objectClasses[$priorityId])) {
            $type = $this->objectClasses[$priorityId];
        } else {
            $type = $this->objectClasses['default'];
        }

        $instance = $this->objectManager->create($type);
        if (!$instance instanceof AbstractCollector) {
            throw new \InvalidArgumentException(
                sprintf('Collector instance does not extend %s.', AbstractCollector::class)
            );
        }

        return $instance;
    }
}
