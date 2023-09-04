<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Magento\Framework\Escaper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;

/**
 * Class Escalate
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Escalate implements CommandInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param TicketManagementInterface $ticketManagement
     * @param Escaper $escaper
     */
    public function __construct(
        TicketManagementInterface $ticketManagement,
        Escaper $escaper
    ) {
        $this->ticketManagement = $ticketManagement;
        $this->escaper = $escaper;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['ticket'])) {
            throw new \InvalidArgumentException('Ticket is required to be escalated');
        }

        if (!isset($data['escalation-message'])) {
            throw new \InvalidArgumentException('Escalation message is required');
        }

        return $this->ticketManagement->escalateTicket(
            $data['ticket']['entity_id'],
            nl2br($this->escaper->escapeHtml($data['escalation-message']))
        );
    }
}
