<?php
namespace Aheadworks\Helpdesk2\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Flag;
use Aheadworks\Helpdesk2\Model\Automation\Task\Runner as TaskRunner;

/**
 * Class RunAutomationTask
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class RunAutomationTask
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
     * @var TaskRunner
     */
    private $taskRunner;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param TaskRunner $taskRunner
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        TaskRunner $taskRunner
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->taskRunner = $taskRunner;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_RUN_AUTOMATION_EXEC_TIME)) {
            try {
                $this->taskRunner->run();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_RUN_AUTOMATION_EXEC_TIME);
        }
    }
}
