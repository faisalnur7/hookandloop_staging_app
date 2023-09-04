<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as Source;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class ChangeStatus implements ActionInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketService;

    /**
     * @var MessageManagement
     */
    private $messageManagement;

    /**
     * @var Source
     */
    private $source;

    /**
     * @param MessageManagement $messageManagement
     * @param Source $source
     * @param TicketManagementInterface $ticketService
     */
    public function __construct(
        MessageManagement $messageManagement,
        Source $source,
        TicketManagementInterface $ticketService
    ) {
        $this->messageManagement = $messageManagement;
        $this->source = $source;
        $this->ticketService = $ticketService;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $ticket = $eventData->getTicket();
        $prevValue = $ticket->getStatusId();
        $newValue = $actionData['value'];

        if ($ticket->getStatusId() == $newValue) {
            return false;
        }

        $ticket->setStatusId($newValue);
        $this->ticketService->updateTicket($ticket);

        $this->messageManagement->createAutomationMessage(
            $ticket->getEntityId(),
            $this->getOptionLabel($prevValue),
            $this->getOptionLabel($newValue),
            $eventData->getEventName()
        );

        return true;
    }

    /**
     * Retrieve option Label by id
     *
     * @param $id
     * @return string
     */
    private function getOptionLabel($id) {
        try {
            return $this->source->getOptionById($id)['label'];
        } catch (LocalizedException $exception) {
            return '';
        }
    }
}
