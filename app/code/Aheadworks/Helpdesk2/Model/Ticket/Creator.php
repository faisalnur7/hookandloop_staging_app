<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketCreatorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Creator
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Creator implements TicketCreatorInterface
{
    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param CommandInterface $saveCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        $this->createCommand = $saveCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Create ticket by order
     *
     * @param OrderInterface $order
     * @return TicketInterface|\Magento\Framework\DataObject
     */
    public function createByOrder(OrderInterface $order)
    {
        $ticketData = [
            TicketInterface::CUSTOMER_ID => $order->getCustomerId(),
            TicketInterface::STORE_ID => $order->getStoreId(),
            TicketInterface::CUSTOMER_NAME => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            TicketInterface::CUSTOMER_EMAIL => $order->getCustomerEmail(),
            TicketInterface::SUBJECT => __('Order #%1 has been referred to review', $order->getIncrementId()),
            MessageInterface::CONTENT => __('You are kindly informed that the order #%1 has just been referred to review.', $order->getIncrementId())
        ];

        $tickedData = $this->postDataProcessor->prepareEntityData($ticketData);
        return $this->createCommand->execute($tickedData);
    }
}
