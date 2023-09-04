<?php
namespace Aheadworks\Helpdesk2\Observer\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class TicketBeforeDeleteObserver
 *
 * @package Aheadworks\Helpdesk2\Observer\Model
 */
class TicketBeforeDeleteObserver implements ObserverInterface
{
    /**
     * @var CommandInterface
     */
    private $removeAttachmentsCommand;

    /**
     * @param CommandInterface $removeAttachmentsCommand
     */
    public function __construct(
        CommandInterface $removeAttachmentsCommand
    ) {
        $this->removeAttachmentsCommand = $removeAttachmentsCommand;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var TicketInterface $ticket */
        $ticket = $observer->getDataObject();
        $this->removeAttachmentsCommand->execute([TicketInterface::ENTITY_ID => $ticket->getEntityId()]);
    }
}
