<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority;

/**
 * Class Urgent
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority
 */
class Urgent extends DefaultPriority
{
    /**
     * @inheritdoc
     */
    protected $rate = 3.0;
}
