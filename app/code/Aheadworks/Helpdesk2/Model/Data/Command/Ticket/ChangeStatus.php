<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Service\TicketService;

class ChangeStatus implements CommandInterface
{
    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @param TicketService $ticketService
     */
    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Mass change status
     *
     * @param array $data
     * @return bool
     */
    public function execute($data)
    {
        $statusId = $data[TicketInterface::STATUS_ID] ?? null;
        if ($statusId === null) {
            throw new \InvalidArgumentException('Status not found');
        }
        $selected = $data['selected'] ?? [];
        foreach ($selected as $ticketId) {
            $this->ticketService->changeStatus((int)$ticketId, (int)$statusId);
        }

        return true;
    }
}
