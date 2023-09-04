<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector;

use Aheadworks\Helpdesk2\Model\Source\Ticket\Status;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;

class StatusHistory extends AbstractCollector
{
    const ASAP_POINTS = 50;
    const URGENT_POINTS = 100;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     */
    public function __construct(
        private readonly MessageCollectionFactory $messageCollectionFactory
    ) {
    }

    /**
     * Get points
     *
     * In M1 we check previous status changing.
     * If it was (WFI => OPEN) than the following code gets executed
     * In M2 we skip it and execute it anyway since we don't store event history.
     * @return int|void
     */
    public function getPoints()
    {
        $currentStatus = $this->ticket->getStatusId();
        $currentPriority = $this->ticket->getPriorityId();

        if (($currentStatus != Status::OPEN)
            || (!in_array($currentPriority, [Priority::ASAP, Priority::URGENT]))
        ) {
            return 0;
        }

        if ($currentPriority == Priority::ASAP) {
            return self::ASAP_POINTS;
        }

        if ($currentPriority == Priority::URGENT) {
            return self::URGENT_POINTS;
        }
    }
}
