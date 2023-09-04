<?php
namespace Aheadworks\Helpdesk2\Observer\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department\TicketChecker;
use Aheadworks\Helpdesk2\Model\Department\ConfigChecker;

/**
 * Class DepartmentBeforeDeleteObserver
 *
 * @package Aheadworks\Helpdesk2\Observer\Model
 */
class DepartmentBeforeDeleteObserver implements ObserverInterface
{
    /**
     * @var TicketChecker
     */
    private $ticketChecker;

    /**
     * @var ConfigChecker
     */
    private $configChecker;

    /**
     * @param TicketChecker $ticketChecker
     * @param ConfigChecker $configChecker
     */
    public function __construct(
        TicketChecker $ticketChecker,
        ConfigChecker $configChecker
    ) {
        $this->ticketChecker = $ticketChecker;
        $this->configChecker = $configChecker;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var DepartmentInterface $department */
        $department = $observer->getDataObject();
        if ($this->ticketChecker->hasTicketsAssigned($department->getId())) {
            throw new LocalizedException(
                __(
                    'Department "%1" has tickets assigned to it and cannot be deleted',
                    $department->getName()
                )
            );
        }

        if ($this->configChecker->isSetAsPrimary($department->getId())) {
            throw new LocalizedException(
                __(
                    'Department "%1" set as primary in the extension settings and cannot be deleted',
                    $department->getName()
                )
            );
        }
    }
}
