<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface TicketCreatorInterface
 * @package Aheadworks\Helpdesk2\Api\Data
 */
interface TicketCreatorInterface
{
    /**
     * Prepare ticket
     *
     * @param OrderInterface $order
     * @return DataObject|TicketInterface
     * @throws LocalizedException
     */
    public function createByOrder(OrderInterface $order);
}
