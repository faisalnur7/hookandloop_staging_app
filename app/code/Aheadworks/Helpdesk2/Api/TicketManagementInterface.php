<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Api;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Interface TicketManagementInterface
 * @api
 */
interface TicketManagementInterface
{
    /**
     * Create new ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createNewTicket($ticket, $message);

    /**
     * Update ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateTicket($ticket);

    /**
     * Create new message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createNewMessage($message);

    /**
     * Escalate ticket
     *
     * @param int $ticketId
     * @param string $escalationMessage
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function escalateTicket($ticketId, $escalationMessage);

    /**
     * Change ticket status
     *
     * @param int $ticketId
     * @param int $statusId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeStatus(int $ticketId, int $statusId): bool;
}
