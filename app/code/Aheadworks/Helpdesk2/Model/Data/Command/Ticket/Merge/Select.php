<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Merge;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\MergeNotice;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\MergeValidator;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\RequestDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\CommentBuilder;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class Select
 */
class Select implements CommandInterface
{
    /**
     * Select constructor.
     *
     * @param CommentBuilder $commentBuilder
     * @param MergeValidator $mergeValidator
     * @param RequestDataProvider $requestDataProvider
     * @param TicketRepositoryInterface $ticketRepository
     * @param MergeNotice $mergeNotice
     */
    public function __construct(
        private CommentBuilder $commentBuilder,
        private MergeValidator $mergeValidator,
        private RequestDataProvider $requestDataProvider,
        private TicketRepositoryInterface $ticketRepository,
        private MergeNotice $mergeNotice
    ) {
    }

    /**
     * Execute command
     *
     * @param array $ticketData
     * @return array
     * @throws LocalizedException
     */
    public function execute($ticketData)
    {
        $result = [];
        $mainTicketUid = $ticketData['main_ticket_uid'] ?? null;
        if (!$mainTicketUid && isset($ticketData['main_ticket_entity_id'])) {
            $mainTicketUid = $this->ticketRepository->getById((int)$ticketData['main_ticket_entity_id'])->getUid();
        }

        $selectedTicketsUid = $ticketData['selected_tickets_uid'] ?? null;
        if ($mainTicketUid && $selectedTicketsUid) {
            $selectedTicketsUid = explode(',', $selectedTicketsUid);
            $selectedTicketsUid = array_unique($selectedTicketsUid);

            $this->mergeValidator->validate($mainTicketUid, $selectedTicketsUid);

            foreach ($selectedTicketsUid as $ticketUid) {
                if ($ticketUid !== $mainTicketUid) {
                    $selectTicket = $this->ticketRepository->getByUid($ticketUid);
                    $result['selected_tickets'][] = $this->requestDataProvider->getPreparedTicketInfo($selectTicket);
                }
            }

            $warningNote = $this->mergeNotice->getWarningNote($mainTicketUid, $selectedTicketsUid);
            if ($warningNote) {
                $result['warning_note'] = $warningNote;
            }

            $result['main_ticket_info'] = $this->requestDataProvider->getPreparedTicketInfo(
                $this->ticketRepository->getByUid($mainTicketUid)
            );
            $result['main_ticket_info']['comment'] = $this->commentBuilder->buildMainTicketComment(
                $mainTicketUid,
                $selectedTicketsUid
            );
        }

        return $result;
    }
}
