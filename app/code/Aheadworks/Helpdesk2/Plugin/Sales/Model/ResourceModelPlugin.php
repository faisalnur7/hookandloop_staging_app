<?php
namespace Aheadworks\Helpdesk2\Plugin\Sales\Model;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Aheadworks\Helpdesk2\Model\Service\TicketService;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;

/**
 * Class ResourceModelPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\Sales\Model
 */
class ResourceModelPlugin
{
    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * Check for order automation
     *
     * @param OrderResourceModel $subject
     * @param OrderResourceModel $result
     * @param Order $order
     * @return OrderResourceModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(OrderResourceModel $subject, $result, $order)
    {
        if ($order->getOrigData(OrderInterface::STATUS) != $order->getStatus()) {
            $this->eventManager->dispatch(
                Event::EVENT_NAME_PREFIX . Event::ORDER_STATUS_CHANGED,
                [
                    'order' => $order
                ]
            );
        }

        return $result;
    }
}
