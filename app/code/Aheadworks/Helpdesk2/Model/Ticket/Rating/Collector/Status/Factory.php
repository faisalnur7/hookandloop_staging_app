<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status
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
     * Create status collector by status ID
     *
     * @param int $statusId
     * @return AbstractCollector
     */
    public function createByStatus($statusId)
    {
        if (isset($this->objectClasses[$statusId])) {
            $type = $this->objectClasses[$statusId];
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
