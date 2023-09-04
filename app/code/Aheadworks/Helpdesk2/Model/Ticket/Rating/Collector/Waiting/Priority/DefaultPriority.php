<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class DefaultPriority
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority
 */
class DefaultPriority extends AbstractCollector
{
    /**
     * @inheritdoc
     */
    protected $rate = 1.0;

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        return $this->calculatePoints();
    }
}
