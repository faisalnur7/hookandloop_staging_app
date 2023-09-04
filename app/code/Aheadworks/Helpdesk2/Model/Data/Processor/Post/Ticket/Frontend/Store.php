<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Store
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend
 */
class Store implements ProcessorInterface
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
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $data[TicketInterface::STORE_ID] = $storeId;

        return $data;
    }
}
