<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority;

/**
 * Class Asap
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority
 */
class Asap extends DefaultPriority
{
    /**
     * @inheritdoc
     */
    protected $rate = 1.5;
}
