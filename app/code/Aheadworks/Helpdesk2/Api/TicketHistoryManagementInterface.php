<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Api;

/**
 * Interface TicketHistoryManagementInterface
 * @api
 */
interface TicketHistoryManagementInterface
{
    /**
     * Get last open tickets for customer
     *
     * @param int $customerId
     * @param null|int $ticketListSize
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastOpenTicketsForCustomer(int $customerId, ?int $ticketListSize = null): array;
}
