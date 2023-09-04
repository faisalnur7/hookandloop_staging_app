<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store as MagentoStore;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Class CanSendEmailToCustomer
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail
 */
class CanSendEmailToCustomer implements CanSendEmailCheckerInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function canSend($actionData, $eventData)
    {
        if ($eventData->getTicket() != null && $eventData->getTicket()->getCustomerEmail() == false) {
            return false;
        }
        $storeIdsToSend = isset($actionData['config'])
        && isset($actionData['config'][Action::STORE_IDS_TO_SEND_EMAIL])
            ? $actionData['config'][Action::STORE_IDS_TO_SEND_EMAIL] : [];

        if (empty($storeIdsToSend)) {
            return true;
        }

        return in_array(MagentoStore::DEFAULT_STORE_ID, $storeIdsToSend)
            || in_array($this->getStoreId($eventData), $storeIdsToSend);
    }

    /**
     * Get store ID
     *
     * @param EventDataInterface $eventData
     * @return int
     * @throws NoSuchEntityException
     */
    private function getStoreId($eventData)
    {
        $storeId = $eventData->getTicket()->getStoreId();
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }
}
