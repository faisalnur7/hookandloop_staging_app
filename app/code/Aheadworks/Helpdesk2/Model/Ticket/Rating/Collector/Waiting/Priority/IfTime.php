<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority;

/**
 * Class IfTime
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority
 */
class IfTime extends DefaultPriority
{
    /**
     * @inheritdoc
     */
    protected $rate = 0.5;
}
