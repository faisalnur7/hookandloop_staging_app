<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Message;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Message
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketManagementInterface $ticketManagement
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TicketManagementInterface $ticketManagement,
        MessageInterfaceFactory $messageFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketManagement = $ticketManagement;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($messageData)
    {
        /** @var MessageInterface $message */
        $message = $this->messageFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $message,
            $messageData,
            MessageInterface::class
        );

        return $this->ticketManagement->createNewMessage($message);
    }
}
