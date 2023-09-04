<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Rating;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

class CollectorComposite implements CollectorInterface
{
    /**
     * @var CollectorInterface[]
     */
    private $collectors;

    /**
     * @param CollectorInterface[] $collectors
     */
    public function __construct(array $collectors = [])
    {
        $this->collectors = $collectors;
    }

    /**
     * Collect rating
     *
     * @param TicketInterface $ticket
     * @return void
     */
    public function collect($ticket)
    {
        foreach ($this->collectors as $collector) {
            $collector->collect($ticket);
        }
    }
}
