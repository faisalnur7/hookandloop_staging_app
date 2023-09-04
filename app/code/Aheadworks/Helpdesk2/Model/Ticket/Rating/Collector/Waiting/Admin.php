<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting;

/**
 * Class Admin
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting
 */
class Admin extends DefaultWaiting
{
    /**
     * @inheritdoc
     */
    protected $rate = 0.5;
}
