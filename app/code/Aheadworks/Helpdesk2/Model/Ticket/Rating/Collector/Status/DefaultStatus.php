<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;

/**
 * Class DefaultStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status
 */
class DefaultStatus extends AbstractCollector
{
    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        return 0;
    }

    /**
     * Is need to apply points to ticket
     *
     * @return bool
     */
    protected function isNeedToApplyPointsToTicket()
    {
        return false;
    }
}
