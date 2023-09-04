<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Cron\Task\Walmart\Listing\SynchronizeInventory;

use Ess\M2ePro\Helper\Component\Walmart;

class ProcessingRunner extends \Ess\M2ePro\Model\Connector\Command\Pending\Processing\Partial\Runner
{
    public const LOCK_ITEM_PREFIX = 'synchronization_walmart_listings_products_update';

    /**
     * @return void
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    protected function setLocks(): void
    {
        parent::setLocks();

        $params = $this->getParams();

        /** @var \Ess\M2ePro\Model\Lock\Item\Manager $lockItemManager */
        $lockItemManager = $this->modelFactory->getObject('Lock_Item_Manager', ['nick' => self::LOCK_ITEM_PREFIX]);
        $lockItemManager->create();

        /** @var \Ess\M2ePro\Model\Account $account */
        $account = $this->parentFactory->getCachedObjectLoaded(Walmart::NICK, 'Account', $params['account_id']);

        $account->addProcessingLock(null, $this->getProcessingObject()->getId());
        $account->addProcessingLock('synchronization', $this->getProcessingObject()->getId());
        $account->addProcessingLock('synchronization_walmart', $this->getProcessingObject()->getId());
        $account->addProcessingLock(self::LOCK_ITEM_PREFIX, $this->getProcessingObject()->getId());
    }

    /**
     * @return void
     * @throws \Ess\M2ePro\Model\Exception\Logic
     */
    protected function unsetLocks(): void
    {
        parent::unsetLocks();

        $params = $this->getParams();

        /** @var \Ess\M2ePro\Model\Lock\Item\Manager $lockItem */
        $lockItem = $this->modelFactory->getObject('Lock_Item_Manager', ['nick' => self::LOCK_ITEM_PREFIX]);
        $lockItem->remove();

        /** @var \Ess\M2ePro\Model\Account $account */
        $account = $this->parentFactory->getCachedObjectLoaded(Walmart::NICK, 'Account', $params['account_id']);

        $account->deleteProcessingLocks(null, $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks('synchronization', $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks('synchronization_walmart', $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks(self::LOCK_ITEM_PREFIX, $this->getProcessingObject()->getId());
    }
}
