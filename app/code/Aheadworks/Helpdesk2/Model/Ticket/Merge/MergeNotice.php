<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MergeNotice
 */
class MergeNotice
{
    /**
     * MergeNotice constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        private TicketRepositoryInterface $ticketRepository
    ) {
    }

    /**
     * Get warning note
     *
     * @param string $mainTicketUid
     * @param array $ticketsToMergeUid
     * @return Phrase|null
     * @throws NoSuchEntityException
     */
    public function getWarningNote(string $mainTicketUid, array $ticketsToMergeUid): ?Phrase
    {
        $isDisplayNote = false;
        $mainTicket = $this->ticketRepository->getByUid($mainTicketUid);
        foreach ($ticketsToMergeUid as $ticketToMergeUid) {
            $ticketToMerge = $this->ticketRepository->getByUid($ticketToMergeUid);
            if ($mainTicket->getCustomerEmail() !== $ticketToMerge->getCustomerEmail()) {
                $isDisplayNote = true;
                break;
            }
        }

        return $isDisplayNote ? __("You're merging ticket information across different customers." .
            " This could result in the unintentional sharing of sensitive data.") : null;
    }
}
