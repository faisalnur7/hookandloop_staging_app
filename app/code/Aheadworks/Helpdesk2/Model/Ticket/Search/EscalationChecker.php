<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Search;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class AttachmentChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Search
 */
class EscalationChecker
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Check if ticket can be escalated
     *
     * @param int|null $storeId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isTicketCanBeEscalated($storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->config->isTicketEscalationEnabled($storeId)
            && !empty($this->config->getEscalationSupervisorEmails($storeId));
    }
}
