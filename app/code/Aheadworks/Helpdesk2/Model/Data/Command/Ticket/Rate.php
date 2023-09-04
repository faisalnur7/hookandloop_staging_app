<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\CustomerRating\CanRateChecker;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;

/**
 * Class Rate
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Rate implements CommandInterface
{
    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var CanRateChecker
     */
    private $checker;

    /**
     * @var Config
     */
    private $config;

    /**
    * @var EventManagerInterface
    */
    private $eventManager;

    /**
     * @param TicketManagementInterface $ticketManagement
     * @param CanRateChecker $canRateChecker
     * @param Config $config
     */
    public function __construct(
        TicketManagementInterface $ticketManagement,
        CanRateChecker $canRateChecker,
        Config $config,
        EventManagerInterface $eventManager
    ) {
        $this->ticketManagement = $ticketManagement;
        $this->checker = $canRateChecker;
        $this->config = $config;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['ticket'])) {
            throw new \InvalidArgumentException('Ticket is required');
        }
        if (!isset($data['rating'])) {
            throw new \InvalidArgumentException('Rating is required');
        }
        if (($data['rating']) == 0) {
            throw new LocalizedException(__('Invalid Customer Rating value'));
        }

        if (!$this->config->isEnabledCustomerRating()) {
            throw new LocalizedException(__('Ticket rating disabled.'));
        }

        /** @var TicketInterface $ticket */
        $ticket = $data['ticket'];

        if (!$this->checker->canRate($ticket)) {
            throw new LocalizedException(__('Rating time has expired.'));
        }

        $ticket->setCustomerRating($data['rating']);
        $this->ticketManagement->updateTicket($ticket);
        $this->eventManager->dispatch(
            Event::EVENT_NAME_PREFIX . Event::TICKET_RATING_CHANGE,
            [
                'ticket' => $ticket
            ]
        );
        return $ticket;
    }
}
