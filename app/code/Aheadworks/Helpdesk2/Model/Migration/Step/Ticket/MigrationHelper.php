<?php
namespace Aheadworks\Helpdesk2\Model\Migration\Step\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as TicketPriority;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class MigrationHelper
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step\Ticket
 */
class MigrationHelper
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var array
     */
    private $statusMap = [
        'open' => TicketStatus::OPEN,
        'pending' => TicketStatus::WAITING,
        'solved' => TicketStatus::CLOSED
    ];

    /**
     * @var array
     */
    private $priorityMap = [
        'low' => TicketPriority::IF_TIME,
        'normal' => TicketPriority::TO_DO,
        'high' => TicketPriority::ASAP
    ];

    /**
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        JsonSerializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Prepare ticket dat
     *
     * @param array $ticketData
     * @return array
     */
    public function prepareTicketData($ticketData)
    {
        $ticketData[TicketInterface::CC_RECIPIENTS] =
            $this->prepareCcRecipients($ticketData[TicketInterface::CC_RECIPIENTS]);
        $ticketData[TicketInterface::STATUS_ID] =
            $this->prepareStatus($ticketData[TicketInterface::STATUS_ID]);
        $ticketData[TicketInterface::PRIORITY_ID] =
            $this->preparePriority($ticketData[TicketInterface::PRIORITY_ID]);

        return $ticketData;
    }

    /**
     * Prepare priority
     *
     * @param string $priority
     * @return string
     */
    public function preparePriority($priority)
    {
        return $this->priorityMap[$priority];
    }

    /**
     * Prepare status
     *
     * @param string $status
     * @return string
     */
    public function prepareStatus($status)
    {
        return $this->statusMap[$status];
    }

    /**
     * Prepare cc recipients
     *
     * @param string $ccRecipients
     * @return string
     */
    public function prepareCcRecipients($ccRecipients)
    {
        try {
            $ccRecipients = $this->jsonSerializer->unserialize(
                (string)$ccRecipients
            );
        } catch (\Exception $exception) {
            $ccRecipients = [];
        }

        return implode(',', $ccRecipients);
    }
}
