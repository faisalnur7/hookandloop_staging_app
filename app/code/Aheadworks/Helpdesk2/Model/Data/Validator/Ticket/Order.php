<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class Order extends AbstractValidator
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Check if agent is correct
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        $orderId = $ticket->getOrderId();
        if ($orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                if ($order->getCustomerId() != $ticket->getCustomerId()) {
                    $this->_addMessages([__('The order does not belong to the customer')]);
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $ticket->setOrderId(null);
            }
        }

        return empty($this->getMessages());
    }
}
