<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Status\Factory as StatusCollectorFactory;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
class Status extends AbstractCollector
{
    /**
     * @var StatusCollectorFactory
     */
    private $statusCollectorFactory;

    /**
     * @param StatusCollectorFactory $statusCollectorFactory
     */
    public function __construct(
        StatusCollectorFactory $statusCollectorFactory
    ) {
        $this->statusCollectorFactory = $statusCollectorFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        $statusCollector = $this->statusCollectorFactory->createByStatus($this->ticket->getStatusId());
        return $statusCollector->collect($this->ticket);
    }
}
