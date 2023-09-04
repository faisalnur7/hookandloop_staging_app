<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Info;

/**
 * Class CommentBuilder
 */
class CommentBuilder
{
    /**
     * CommentBuilder constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     * @param Info $messageInfo
     */
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private Info $messageInfo
    ) {
    }

    /**
     * Build main ticket comment for merging
     *
     * @param string $mainTicketUid
     * @param array $ticketsToMergeUid
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function buildMainTicketComment(string $mainTicketUid, array $ticketsToMergeUid): string
    {
        $result = '';
        $mainTicket = $this->ticketRepository->getByUid($mainTicketUid);
        $mainTicketSign = '#' . $mainTicket->getUid() . ' "' . $mainTicket->getSubject() . '"';
        foreach ($ticketsToMergeUid as $ticketToMergeUid) {
            $ticketToMerge = $this->ticketRepository->getByUid($ticketToMergeUid);
            $ticketToMergeSign = '#' . $ticketToMerge->getUid() . ' "' . $ticketToMerge->getSubject() . '"';
            $result .= __(
                'Request %1 was closed and merged into this request %2.',
                $ticketToMergeSign,
                $mainTicketSign
            );
            $result .= PHP_EOL;
            $lastComment = $this->messageInfo->getLastMessage((int)$ticketToMerge->getEntityId())->getContent();
            if ($lastComment) {
                $result .= __('Last comment in request %1:', $ticketToMergeSign);
                $result .= PHP_EOL;
                $result .= $lastComment . PHP_EOL;
            }
        }
        return $result;
    }
}
