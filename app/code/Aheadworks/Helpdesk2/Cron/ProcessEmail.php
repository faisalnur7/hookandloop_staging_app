<?php
namespace Aheadworks\Helpdesk2\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\GatewayManagementInterface;
use Aheadworks\Helpdesk2\Model\Flag;

/**
 * Class ProcessEmail
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class ProcessEmail
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var GatewayManagementInterface
     */
    private $gatewayManagement;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param GatewayManagementInterface $gatewayManagement
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        GatewayManagementInterface $gatewayManagement
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->gatewayManagement = $gatewayManagement;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_PROCESS_EMAIL_LAST_EXEC_TIME, 300)) {
            try {
                $this->gatewayManagement->processEmails();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_PROCESS_EMAIL_LAST_EXEC_TIME);
        }
    }
}
