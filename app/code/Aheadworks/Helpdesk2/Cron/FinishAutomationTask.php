<?php
namespace Aheadworks\Helpdesk2\Cron;

use Psr\Log\LoggerInterface;
use Aheadworks\Helpdesk2\Model\Flag;
use Aheadworks\Helpdesk2\Model\Automation\Task\Finisher as TaskFinisher;

/**
 * Class FinishAutomationTask
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class FinishAutomationTask
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
     * @var TaskFinisher
     */
    private $taskFinisher;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param TaskFinisher $taskFinisher
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        TaskFinisher $taskFinisher
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->taskFinisher = $taskFinisher;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_FINISH_AUTOMATION_EXEC_TIME)) {
            try {
                $this->taskFinisher->finish();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_FINISH_AUTOMATION_EXEC_TIME);
        }
    }
}
