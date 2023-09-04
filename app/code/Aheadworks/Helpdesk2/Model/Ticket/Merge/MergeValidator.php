<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class MergeValidator
 */
class MergeValidator
{
    /**
     * Is enable tickets to merge max count validation
     */
    const IS_ENABLE_TICKETS_MAX_COUNT_VALIDATION = false;

    /**
     * Tickets to merge max count
     */
    const TICKETS_TO_MERGE_MAX_COUNT = 2;

    /**
     * MergeValidator constructor.
     *
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        private TicketCollectionFactory $ticketCollectionFactory,
        private TicketRepositoryInterface $ticketRepository
    ) {
    }

    /**
     * Validate
     *
     * @param string $mainTicketUid
     * @param array $ticketsUidToMerge
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return void
     */
    public function validate(string $mainTicketUid, array $ticketsUidToMerge): void
    {
        if (self::IS_ENABLE_TICKETS_MAX_COUNT_VALIDATION &&
            count($ticketsUidToMerge) > self::TICKETS_TO_MERGE_MAX_COUNT) {
            throw new LocalizedException(
                __('The maximum number of tickets to merge is "%1"', self::TICKETS_TO_MERGE_MAX_COUNT)
            );
        }

        $mainTicket = $this->ticketRepository->getByUid($mainTicketUid);
        $mainTicketEmails = array_merge(
            [$mainTicket->getCustomerEmail()],
            explode(',', (string)$mainTicket->getCcRecipients())
        );

        $ticketsToMerge = $this->ticketCollectionFactory->create()
            ->addFieldToFilter(
                TicketInterface::UID, ['in' => $ticketsUidToMerge]
            )->getItems();

        if (!count($ticketsToMerge)) {
            throw new LocalizedException(
                __('Merge tickets not found!')
            );
        }

        $closedTicketsUid = [];
        $hasNotSameEmailTicketsUid = [];
        foreach ($ticketsToMerge as $ticketToMerge) {
            /** @var TicketInterface $ticketToMerge */
            if ((int)$ticketToMerge->getStatusId() === TicketStatus::CLOSED) {
                $closedTicketsUid[] = $ticketToMerge->getUid();
            }
            $ccRecipients = explode(',', (string)$ticketToMerge->getCcRecipients());
            if (!in_array($ticketToMerge->getCustomerEmail(), $mainTicketEmails) &&
                !array_intersect($ccRecipients, $mainTicketEmails)) {
                $hasNotSameEmailTicketsUid[] = $ticketToMerge->getUid();
            }
        }

        if ($hasNotSameEmailTicketsUid) {
            throw new LocalizedException(
                __(
                    'Need at least 1 email to be the same in both tickets. Invalid tickets "%1"',
                    implode(',', $hasNotSameEmailTicketsUid)
                )
            );
        }

        if ($closedTicketsUid) {
            throw new LocalizedException(
                __(
                    'Tickets "%1" have closed status. Please select others.',
                    implode(',', $closedTicketsUid)
                )
            );
        }
    }
}
