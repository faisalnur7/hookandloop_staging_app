<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\DateTime\Formatter;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class RequestDataProvider
 */
class RequestDataProvider
{
    /**
     * @var TicketInterface
     */
    private $mainTicket;

    /**
     * @var TicketInterface[]
     */
    private $ticketsToMerge;

    /**
     * RequestDataProvider constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketInfoInterfaceFactory $ticketInfoInterfaceFactory
     * @param Formatter $dateFormatter
     */
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private DataObjectHelper $dataObjectHelper,
        private TicketInfoInterfaceFactory $ticketInfoInterfaceFactory,
        private Formatter $dateFormatter
    ) {
    }

    /**
     * Prepare merge data
     *
     * @param array $requestData
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareMergeData(array $requestData): RequestDataProvider
    {
        $mergeData = $requestData['merge_data'];

        $isSourceTicketView = isset($requestData['merge_source']) && $requestData['merge_source'] === 'ticket-view';
        if ($isSourceTicketView) {
            $mainTicketId = $requestData['entity_id'];
        } else {
            $mainTicketId = $mergeData['main_ticket_data']['entity_id'];
        }

        $mainTicket = $this->ticketRepository->getById($mainTicketId);
        if (isset($mergeData['main_ticket_data']['comment'])) {
            $mainTicket->setData('merge_comment', $mergeData['main_ticket_data']['comment']);
        }

        if (isset($mergeData['main_ticket_data']['is_requested_see_comment'])) {
            $isRequestedSeeComment = filter_var(
                $mergeData['main_ticket_data']['is_requested_see_comment'],
                FILTER_VALIDATE_BOOLEAN
            );
            $mainTicket->setData(
                'is_requested_see_merge_comment',
                $isRequestedSeeComment
            );
        }

        $ticketsToMerge = [];
        foreach ($mergeData['merge_tickets'] as $mergeTicket) {
            $ticketToMerge = $this->ticketRepository->getById($mergeTicket['entity_id']);
            if (isset($mergeTicket['comment'])) {
                $ticketToMerge->setData('merge_comment', $mergeTicket['comment']);
            }

            if (isset($mergeTicket['is_requested_see_comment'])) {
                $isRequestedSeeComment = filter_var(
                    $mergeTicket['is_requested_see_comment'],
                    FILTER_VALIDATE_BOOLEAN
                );
                $ticketToMerge->setData(
                    'is_requested_see_merge_comment',
                    $isRequestedSeeComment
                );
            }
            $ticketsToMerge[] = $ticketToMerge;
        }

        $this->ticketsToMerge = $ticketsToMerge;
        $this->mainTicket = $mainTicket;

        return $this;
    }

    /**
     * Get tickets to merge
     *
     * @return TicketInterface[]
     */
    public function getTicketsToMerge(): array
    {
        return $this->ticketsToMerge;
    }

    /**
     * Get main ticket
     *
     * @return TicketInterface|null
     */
    public function getMainTicket(): ?TicketInterface
    {
        return $this->mainTicket;
    }

    /**
     * Get prepared ticket info
     *
     * @param TicketInterface $ticket
     * @return array|null
     * @throws \Exception
     */
    public function getPreparedTicketInfo(TicketInterface $ticket): ?array
    {
        $ticketInfo = $this->ticketInfoInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticketInfo,
            $ticket->toArray(),
            TicketInfoInterface::class
        );
        $ticketInfo->setCreatedAt(
            $ticketInfo->getCreatedAt() ? $this->prepareDate($ticketInfo->getCreatedAt()) : null
        );
        return $ticketInfo->getData();
    }

    /**
     * Prepare date
     *
     * @param string $date
     * @return string
     * @throws \Exception
     */
    private function prepareDate(string $date): string
    {
        return $this->dateFormatter->convertDateToFormat($date);
    }
}
