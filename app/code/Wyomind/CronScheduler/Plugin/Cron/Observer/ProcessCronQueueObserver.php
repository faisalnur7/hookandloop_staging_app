<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\CronScheduler\Plugin\Cron\Observer;

use Magento\Cron\Model\DeadlockRetrierInterface;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Profiler\Driver\Standard\Stat;
use Magento\Framework\Profiler\Driver\Standard\StatFactory;

/**
 * Magento 2.3.5
 * 
 */
class ProcessCronQueueObserver extends \Magento\Cron\Observer\ProcessCronQueueObserver
{
    public $_jobStatus;
    public $_taskHelper;
    /**#@+
     * Cache key values
     */
    const CACHE_KEY_LAST_SCHEDULE_GENERATE_AT = 'cron_last_schedule_generate_at';

    const CACHE_KEY_LAST_HISTORY_CLEANUP_AT = 'cron_last_history_cleanup_at';

    /**
     * Flag for internal communication between processes for running
     * all jobs in a group in parallel as a separate process
     */
    const STANDALONE_PROCESS_STARTED = 'standaloneProcessStarted';

    /**#@-*/

    /**#@+
     * List of configurable constants used to calculate and validate during handling cron jobs
     */
    const XML_PATH_SCHEDULE_GENERATE_EVERY = 'schedule_generate_every';

    const XML_PATH_SCHEDULE_AHEAD_FOR = 'schedule_ahead_for';

    const XML_PATH_SCHEDULE_LIFETIME = 'schedule_lifetime';

    const XML_PATH_HISTORY_CLEANUP_EVERY = 'history_cleanup_every';

    const XML_PATH_HISTORY_SUCCESS = 'history_success_lifetime';

    const XML_PATH_HISTORY_FAILURE = 'history_failure_lifetime';

    /**#@-*/

    /**
     * Value of seconds in one minute
     */
    const SECONDS_IN_MINUTE = 60;

    /**
     * How long to wait for cron group to become unlocked
     */
    const LOCK_TIMEOUT = 60;

    /**
     * Static lock prefix for cron group locking
     */
    const LOCK_PREFIX = 'CRON_';

    /**
     * Timer ID for profiling
     */
    const CRON_TIMERID = 'job %s';

    /**
     * Max retries for acquire locks for cron jobs
     */
    const MAX_RETRIES = 5;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected $_pendingSchedules;

    /**
     * @var \Magento\Cron\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ScheduleFactory
     */
    protected $_scheduleFactory;

    /**
     * @var \Magento\Framework\App\Console\Request
     */
    protected $_request;

    /**
     * @var \Magento\Framework\ShellInterface
     */
    protected $_shell;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Symfony\Component\Process\PhpExecutableFinder
     */
    protected $phpExecutableFinder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\Lock\LockManagerInterface
     */
    private $lockManager;

    /**
     * @var array
     */
    private $invalid = [];

    /**
     * @var Stat
     */
    private $statProfiler;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var DeadlockRetrierInterface
     */
    private $retrier;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Cron\Model\ScheduleFactory $scheduleFactory
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Cron\Model\ConfigInterface $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Console\Request $request
     * @param \Magento\Framework\ShellInterface $shell
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Process\PhpExecutableFinderFactory $phpExecutableFinderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param State $state
     * @param StatFactory $statFactory
     * @param \Magento\Framework\Lock\LockManagerInterface $lockManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param DeadlockRetrierInterface $retrier
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Cron\Model\ConfigInterface $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Console\Request $request,
        \Magento\Framework\ShellInterface $shell,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Process\PhpExecutableFinderFactory $phpExecutableFinderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Event\Manager $eventManager,
        \Wyomind\CronScheduler\Helper\Task $taskHelper,
        \Magento\Framework\Profiler\Driver\Standard\StatFactory $statFactory,
        \Magento\Framework\Lock\LockManagerInterface $lockManager,
        \Magento\Cron\Model\DeadlockRetrierInterface $retrier
    ) {
    

        $construct = "__construct"; // in order to bypass the compiler
        parent::$construct($objectManager, $scheduleFactory, $cache, $config, $scopeConfig, $request, $shell, $dateTime, $phpExecutableFinderFactory, $logger, $state, $statFactory, $lockManager, $eventManager, $retrier);
        $this->logger = $logger; // because private
        $this->state = $state; // because private
        $this->lockManager = $lockManager; //because private
        $this->statProfiler = $statFactory->create(); // because private
        $this->retrier = $retrier; // because private
        $this->eventManager = $eventManager;
        $this->_taskHelper = $taskHelper;
        $jobGroupsRoot = $this->_config->getJobs();
        $groups = array_values($jobGroupsRoot);
        foreach (array_values($groups) as $jobs) {
            foreach ($jobs as $job) {
                if (isset($job['code'])) {
                    $this->_jobStatus[$job['code']] = isset($job['status']) ? $job['status'] : 1;
                } elseif (isset($job['name'])) {
                    $this->_jobStatus[$job['name']] = isset($job['status']) ? $job['status'] : 1;
                }
            }
        }
    }

    /**
     * @param \Magento\Cron\Observer\ProcessCronQueueObserver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Cron\Observer\ProcessCronQueueObserver $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer
    ) {
    
        // <CRONSCHEDULER>
        // current task ran
        $currentSchedule = null;

        // set the shutdown/error_handler functions to catch a task that throws a fatal error (like parsing error)
        register_shutdown_function(function () use (&$currentSchedule) {
            $lastError = error_get_last();
            if ($lastError && strpos($lastError['message'], 'mcrypt') === false && strpos($lastError['message'], 'mdecrypt') === false) {
                if ($currentSchedule != null) {
                    $s = $currentSchedule;
                    $s->setMessages($lastError['message']);
                    $s->setStatus(\Magento\Cron\Model\Schedule::STATUS_ERROR);
                    $s->setErrorFile($lastError['file']);
                    $s->setErrorLine($lastError['line']);
                    $this->_taskHelper->setTrace($s);
                    $s->save();
                    $this->eventManager->dispatch('cronscheduler_task_failed', ['task' => $s]);
                }
            }
        });
        set_error_handler(function (
            $errorLevel,
            $errorMessage,
            $errorFile,
            $errorLine,
            $errorContext = null
        ) use (&$currentSchedule) {
            // errorContext:
            // This parameter has been DEPRECATED as of PHP 7.2.0, and REMOVED as of PHP 8.0.0. If your function defines this parameter without a default, an error of "too few arguments" will be raised when it is called.

            if ($errorLevel != "" && $currentSchedule != null && strpos($errorMessage, 'mcrypt') === false && strpos($errorMessage, 'mdecrypt') === false) {
                $s = $currentSchedule;
                $s->setMessages($errorMessage);
                $s->setStatus(\Magento\Cron\Model\Schedule::STATUS_ERROR);
                $s->setErrorFile($errorFile);
                $s->setErrorLine($errorLine);
                $this->_taskHelper->setTrace($s);
                $s->save();
                $this->eventManager->dispatch('cronscheduler_task_failed', ['task' => $s]);
            }
        });
        // </CRONSCHEDULER>


        $currentTime = $this->dateTime->gmtTimestamp();
        $jobGroupsRoot = $this->_config->getJobs();
        // sort jobs groups to start from used in separated process
        uksort(
            $jobGroupsRoot,
            function ($a, $b) {
                return $this->getCronGroupConfigurationValue($b, 'use_separate_process')
                    - $this->getCronGroupConfigurationValue($a, 'use_separate_process');
            }
        );

        $phpPath = $this->phpExecutableFinder->find() ?: 'php';

        foreach ($jobGroupsRoot as $groupId => $jobsRoot) {
            if (!$this->isGroupInFilter($groupId)) {
                continue;
            }
            if ($this->_request->getParam(self::STANDALONE_PROCESS_STARTED) !== '1'
                && $this->getCronGroupConfigurationValue($groupId, 'use_separate_process') == 1
            ) {
                $this->_shell->execute(
                    $phpPath . ' %s cron:run --group=' . $groupId . ' --' . Cli::INPUT_KEY_BOOTSTRAP . '='
                    . self::STANDALONE_PROCESS_STARTED . '=1',
                    [
                        BP . '/bin/magento'
                    ]
                );
                continue;
            }

            $this->lockGroup(
                $groupId,
                function ($groupId) use ($currentTime) {
                    $this->cleanupJobs($groupId, $currentTime);
                    $this->generateSchedules($groupId);
                }
            );
            $this->processPendingJobs($groupId, $jobsRoot, $currentTime);
        }
    }

    /**
     * Lock group
     *
     * It should be taken by standalone (child) process, not by the parent process.
     *
     * @param int $groupId
     * @param callable $callback
     *
     * @return void
     */
    private function lockGroup($groupId, callable $callback)
    {
        if (!$this->lockManager->lock(self::LOCK_PREFIX . $groupId, self::LOCK_TIMEOUT)) {
            $this->logger->warning(
                sprintf(
                    "Could not acquire lock for cron group: %s, skipping run",
                    $groupId
                )
            );
            return;
        }
        try {
            $callback($groupId);
        } finally {
            $this->lockManager->unlock(self::LOCK_PREFIX . $groupId);
        }
    }

    /**
     * @param string $groupId
     * @param array $jobsRoot
     * @param int $currentTime
     * @throws \Exception
     */
    protected function _runJob($scheduledTime, $currentTime, $jobConfig, $schedule, $groupId)
    {
        $jobCode = $schedule->getJobCode();
        $scheduleLifetime = $this->getCronGroupConfigurationValue($groupId, self::XML_PATH_SCHEDULE_LIFETIME);
        $scheduleLifetime = $scheduleLifetime * self::SECONDS_IN_MINUTE;
        if ($scheduledTime < $currentTime - $scheduleLifetime) {
            $schedule->setStatus(Schedule::STATUS_MISSED);
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception(sprintf('Cron Job %s is missed at %s', $jobCode, $schedule->getScheduledAt()));
        }

        if (!isset($jobConfig['instance'], $jobConfig['method'])) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception(sprintf('No callbacks found for cron job %s', $jobCode));
        }
        $model = $this->_objectManager->create($jobConfig['instance']);
        $callback = [$model, $jobConfig['method']];
        if (!is_callable($callback)) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception(
                sprintf('Invalid callback: %s::%s can\'t be called', $jobConfig['instance'], $jobConfig['method'])
            );
        }

        $schedule->setExecutedAt(date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()));
        $this->retrier->execute(
            function () use ($schedule) {
                $schedule->save();
            },
            $schedule->getResource()->getConnection()
        );

        $this->startProfiling($jobCode);
        $this->eventManager->dispatch('cron_job_run', ['job_name' => 'cron/' . $groupId . '/' . $jobCode]);

        try {
            $this->logger->info(sprintf('Cron Job %s is run', $jobCode));
            //phpcs:ignore Magento2.Functions.DiscouragedFunction
            call_user_func_array($callback, [$schedule]);
        } catch (\Throwable $e) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            $this->logger->error(
                sprintf(
                    'Cron Job %s has an error: %s. Statistics: %s',
                    $jobCode,
                    $e->getMessage(),
                    $this->getProfilingStat($jobCode)
                )
            );
            if (!$e instanceof \Exception) {
                $e = new \RuntimeException(
                    'Error when running a cron job',
                    0,
                    $e
                );
            }
            throw $e;
        } finally {
            $this->stopProfiling($jobCode);
        }

        $schedule->setStatus(
            Schedule::STATUS_SUCCESS
        )->setFinishedAt(
            date(
                'Y-m-d H:i:s',
                $this->dateTime->gmtTimestamp()
            )
        );

        $this->logger->info(
            sprintf(
                'Cron Job %s is successfully finished. Statistics: %s',
                $jobCode,
                $this->getProfilingStat($jobCode)
            )
        );
    }

    /**
     * Starts profiling
     *
     * @return void
     */
    private function startProfiling(string $jobName = ''): void
    {
        $this->statProfiler->clear();
        $this->statProfiler->start(
            sprintf(self::CRON_TIMERID, $jobName),
            microtime(true),
            memory_get_usage(true),
            memory_get_usage()
        );
    }

    /**
     * Stops profiling
     *
     * @param string $jobName
     * @return void
     */
    private function stopProfiling(string $jobName = ''): void
    {
        $this->statProfiler->stop(
            sprintf(self::CRON_TIMERID, $jobName),
            microtime(true),
            memory_get_usage(true),
            memory_get_usage()
        );
    }

    /**
     * Retrieves statistics in the JSON format
     *
     * @param string $jobName
     * @return string
     */
    private function getProfilingStat(string $jobName): string
    {
        $stat = $this->statProfiler->get(
            sprintf(self::CRON_TIMERID, $jobName)
        );
        unset($stat[Stat::START]);
        return json_encode($stat);
    }


    /**
     * Return job collection from data base with status 'pending'.
     *
     * @param string $groupId
     * @return \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    private function getPendingSchedules($groupId)
    {
        $jobs = $this->_config->getJobs();
        $pendingJobs = $this->_scheduleFactory->create()->getCollection();
        $pendingJobs->addFieldToFilter('status', Schedule::STATUS_PENDING);
        $pendingJobs->addFieldToFilter('job_code', ['in' => array_keys($jobs[$groupId])]);
        return $pendingJobs;
    }

    /**
     * Generate cron schedule
     *
     * @param string $groupId
     * @return $this
     */
    private function generateSchedules(string $groupId)
    {
        /**
         * check if schedule generation is needed
         */
        $lastRun = (int)$this->_cache->load(self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT . $groupId);
        $rawSchedulePeriod = (int)$this->getCronGroupConfigurationValue(
            $groupId,
            self::XML_PATH_SCHEDULE_GENERATE_EVERY
        );
        $schedulePeriod = $rawSchedulePeriod * self::SECONDS_IN_MINUTE;
        if ($lastRun > $this->dateTime->gmtTimestamp() - $schedulePeriod) {
            return $this;
        }

        /**
         * save time schedules generation was ran with no expiration
         */
        $this->_cache->save(
            $this->dateTime->gmtTimestamp(),
            self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT . $groupId,
            ['crontab'],
            null
        );

        $schedules = $this->getPendingSchedules($groupId);
        $exists = [];
        /** @var Schedule $schedule */
        foreach ($schedules as $schedule) {
            $exists[$schedule->getJobCode() . '/' . $schedule->getScheduledAt()] = 1;
        }

        /**
         * generate global crontab jobs
         */
        $jobs = $this->_config->getJobs();
        $this->invalid = [];
        $this->_generateJobs($jobs[$groupId], $exists, $groupId);
        $this->cleanupScheduleMismatches();

        return $this;
    }

    /**
     * Generate jobs for config information
     *
     * @param   array $jobs
     * @param   array $exists
     * @param   string $groupId
     * @return  void
     */
    protected function _generateJobs($jobs, $exists, $groupId)
    {
        foreach ($jobs as $jobCode => $jobConfig) {
            if (isset($jobConfig['status']) && $jobConfig['status'] == 0) {
                continue;
            }
            $cronExpression = $this->getCronExpression($jobConfig);
            if (!$cronExpression) {
                continue;
            }

            $timeInterval = $this->getScheduleTimeInterval($groupId);
            $this->saveSchedule($jobCode, $cronExpression, $timeInterval, $exists);
        }
    }

    /**
     * Clean expired jobs
     *
     * @param string $groupId
     * @param int $currentTime
     * @return void
     */
    private function cleanupJobs(string $groupId, int $currentTime)
    {
        // check if history cleanup is needed
        $lastCleanup = (int)$this->_cache->load(self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT . $groupId);
        $historyCleanUp = (int)$this->getCronGroupConfigurationValue($groupId, self::XML_PATH_HISTORY_CLEANUP_EVERY);
        if ($lastCleanup > $this->dateTime->gmtTimestamp() - $historyCleanUp * self::SECONDS_IN_MINUTE) {
            return;
        }
        // save time history cleanup was ran with no expiration
        $this->_cache->save(
            $this->dateTime->gmtTimestamp(),
            self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT . $groupId,
            ['crontab'],
            null
        );

        $this->cleanupDisabledJobs($groupId);
        $this->cleanupRunningJobs($groupId);

        $historySuccess = (int)$this->getCronGroupConfigurationValue($groupId, self::XML_PATH_HISTORY_SUCCESS);
        $historyFailure = (int)$this->getCronGroupConfigurationValue($groupId, self::XML_PATH_HISTORY_FAILURE);
        $historyLifetimes = [
            Schedule::STATUS_SUCCESS => $historySuccess * self::SECONDS_IN_MINUTE,
            Schedule::STATUS_MISSED => $historyFailure * self::SECONDS_IN_MINUTE,
            Schedule::STATUS_ERROR => $historyFailure * self::SECONDS_IN_MINUTE,
            Schedule::STATUS_PENDING => max($historyFailure, $historySuccess) * self::SECONDS_IN_MINUTE,
        ];

        $jobs = $this->_config->getJobs()[$groupId];
        $count = 0;
        foreach ($historyLifetimes as $status => $time) {
            $count += $this->cleanup(
                [
                    'status = ?' => $status,
                    'job_code in (?)' => array_keys($jobs),
                    'scheduled_at < ?' => $this->_scheduleFactory
                        ->create()
                        ->getResource()
                        ->getConnection()
                        ->formatDate($currentTime - $time)
                ]
            );
        }

        if ($count) {
            $this->logger->info(sprintf('%d cron jobs were cleaned', $count));
        }
    }

    private function cleanupRunningJobs(string $groupId): void
    {
        $scheduleResource = $this->_scheduleFactory->create()->getResource();
        $connection = $scheduleResource->getConnection();

        $jobs = $this->_config->getJobs();

        $connection->update(
            $scheduleResource->getTable('cron_schedule'),
            [
                'status' => \Magento\Cron\Model\Schedule::STATUS_ERROR,
                'messages' => 'Time out'
            ],
            [
                $connection->quoteInto('status = ?', \Magento\Cron\Model\Schedule::STATUS_RUNNING),
                $connection->quoteInto('job_code IN (?)', array_keys($jobs[$groupId])),
                'scheduled_at < UTC_TIMESTAMP() - INTERVAL 1 DAY'
            ]
        );
    }

    /**
     * Get config of schedule.
     *
     * @param array $jobConfig
     * @return mixed
     */
    protected function getConfigSchedule($jobConfig)
    {
        $cronExpr = $this->_scopeConfig->getValue(
            $jobConfig['config_path'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $cronExpr;
    }

    /**
     * Save a schedule of cron job.
     *
     * @param string $jobCode
     * @param string $cronExpression
     * @param int $timeInterval
     * @param array $exists
     * @return void
     */
    protected function saveSchedule($jobCode, $cronExpression, $timeInterval, $exists)
    {
        $currentTime = $this->dateTime->gmtTimestamp();
        $timeAhead = $currentTime + $timeInterval;
        for ($time = $currentTime; $time < $timeAhead; $time += self::SECONDS_IN_MINUTE) {
            $scheduledAt = date('Y-m-d H:i:00', $time);
            $alreadyScheduled = !empty($exists[$jobCode . '/' . $scheduledAt]);
            $schedule = $this->createSchedule($jobCode, $cronExpression, $time);
            $valid = $schedule->trySchedule();
            if (!$valid) {
                if ($alreadyScheduled) {
                    if (!isset($this->invalid[$jobCode])) {
                        $this->invalid[$jobCode] = [];
                    }
                    $this->invalid[$jobCode][] = $scheduledAt;
                }
                continue;
            }
            if (!$alreadyScheduled) {
                // time matches cron expression
                $schedule->save();
            }
        }
    }

    /**
     * Create a schedule of cron job.
     *
     * @param string $jobCode
     * @param string $cronExpression
     * @param int $time
     * @return Schedule
     */
    protected function createSchedule($jobCode, $cronExpression, $time)
    {
        $schedule = $this->_scheduleFactory->create()
            ->setCronExpr($cronExpression)
            ->setJobCode($jobCode)
            ->setStatus(Schedule::STATUS_PENDING)
            ->setCreatedAt(date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()))
            ->setScheduledAt(date('Y-m-d H:i', $time));

        return $schedule;
    }

    /**
     * Get time interval for scheduling.
     *
     * @param string $groupId
     * @return int
     */
    protected function getScheduleTimeInterval($groupId)
    {
        $scheduleAheadFor = (int)$this->getCronGroupConfigurationValue($groupId, self::XML_PATH_SCHEDULE_AHEAD_FOR);
        $scheduleAheadFor = $scheduleAheadFor * self::SECONDS_IN_MINUTE;

        return $scheduleAheadFor;
    }

    /**
     * Clean up scheduled jobs that are disabled in the configuration.
     *
     * This can happen when you turn off a cron job in the config and flush the cache.
     *
     * @param string $groupId
     * @return void
     */
    private function cleanupDisabledJobs($groupId)
    {
        $jobs = $this->_config->getJobs();
        $jobsToCleanup = [];
        foreach ($jobs[$groupId] as $jobCode => $jobConfig) {
            if (!$this->getCronExpression($jobConfig)) {
                /** @var \Magento\Cron\Model\ResourceModel\Schedule $scheduleResource */
                $jobsToCleanup[] = $jobCode;
            }
        }

        if (count($jobsToCleanup) > 0) {
            $count = $this->cleanup(
                [
                    'status = ?' => Schedule::STATUS_PENDING,
                    'job_code in (?)' => $jobsToCleanup,
                ]
            );

            $this->logger->info(sprintf('%d cron jobs were cleaned', $count));
        }
    }

    /**
     * Get cron expression of cron job.
     *
     * @param array $jobConfig
     * @return null|string
     */
    private function getCronExpression($jobConfig)
    {
        $cronExpression = null;
        if (isset($jobConfig['config_path'])) {
            $cronExpression = $this->getConfigSchedule($jobConfig) ?: null;
        }

        if (!$cronExpression) {
            if (isset($jobConfig['schedule'])) {
                $cronExpression = $jobConfig['schedule'];
            }
        }
        return $cronExpression;
    }

    /**
     * Clean up scheduled jobs that do not match their cron expression anymore.
     *
     * This can happen when you change the cron expression and flush the cache.
     *
     * @return $this
     */
    private function cleanupScheduleMismatches()
    {
        foreach ($this->invalid as $jobCode => $scheduledAtList) {
            $this->cleanup(
                [
                    'status = ?' => Schedule::STATUS_PENDING,
                    'job_code = ?' => $jobCode,
                    'scheduled_at in (?)' => $scheduledAtList,
                ]
            );
        }

        return $this;
    }

    /**
     * Get CronGroup Configuration Value.
     *
     * @param string $groupId
     * @param string $path
     * @return int
     */
    private function getCronGroupConfigurationValue($groupId, $path)
    {
        return $this->_scopeConfig->getValue(
            'system/cron/' . $groupId . '/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Group In Filter.
     *
     * @param string $groupId
     * @return bool
     */
    private function isGroupInFilter($groupId): bool
    {
        return !($this->_request->getParam('group') !== null
            && trim($this->_request->getParam('group'), "'") !== $groupId);
    }

    /**
     * @param int $groupId
     * @param callable $callback
     */
    private function processPendingJobs($groupId, $jobsRoot, $currentTime)
    {
        $jobGroupsRoot = $this->_config->getJobs();
        $procesedJobs = [];
        $pendingJobs = $this->getPendingSchedules($groupId);
        /** @var Schedule $schedule */
        foreach ($pendingJobs as $schedule) {
            // <CRONSCHEDULER>
            // set the current task running
            $currentSchedule = $schedule;
            $this->eventManager->dispatch('cronscheduler_task_run_before', ['task' => $schedule]);
            // </CRONSCHEDULER>

            if (isset($procesedJobs[$schedule->getJobCode()])) {
                // process only on job per run
                continue;
            }
            $jobConfig = isset($jobsRoot[$schedule->getJobCode()]) ? $jobsRoot[$schedule->getJobCode()] : null;
            if (!$jobConfig /* <CRONSCHEDULER> */ || isset($jobConfig['status']) && $jobConfig['status'] == 0 /* </CRONSCHEDULER> */) {
                continue;
            }

            $scheduledTime = strtotime($schedule->getScheduledAt());
            if ($scheduledTime > $currentTime) {
                continue;
            }

            $this->tryRunJob($scheduledTime, $currentTime, $jobConfig, $schedule, $groupId);

            if ($schedule->getStatus() === Schedule::STATUS_SUCCESS) {
                $processedJobs[$schedule->getJobCode()] = true;
            }

            $this->retrier->execute(
                function () use ($schedule) {
                    $schedule->save();
                },
                $schedule->getResource()->getConnection()
            );
        }
    }

    /**
     * Try to acquire lock for cron job and try to run this job.
     *
     * @param int $scheduledTime
     * @param int $currentTime
     * @param string[] $jobConfig
     * @param Schedule $schedule
     * @param string $groupId
     */
    private function tryRunJob($scheduledTime, $currentTime, $jobConfig, $schedule, $groupId)
    {
        // use sha1 to limit length
        // phpcs:ignore Magento2.Security.InsecureFunction
        $md5 = "md5";
        $lockName = self::LOCK_PREFIX . $md5($groupId . '_' . $schedule->getJobCode());

        try {
            for ($retries = self::MAX_RETRIES; $retries > 0; $retries--) {
                if ($this->lockManager->lock($lockName, 0) && $schedule->tryLockJob()) {
                    // <CRONSCHEDULER>
                    $this->eventManager->dispatch('cronscheduler_task_run', ['task' => $schedule]);
                    // </CRONSCHEDULER>
                    $this->_runJob($scheduledTime, $currentTime, $jobConfig, $schedule, $groupId);
                    // <CRONSCHEDULER>
                    $this->_taskHelper->setTrace($schedule);
                    $schedule->save();
                    $this->eventManager->dispatch('cronscheduler_task_success', ['task' => $schedule]);
                    // </CRONSCHEDULER>
                    break;
                }
                $this->logger->warning("Could not acquire lock for cron job: {$schedule->getJobCode()}");
            }
        } catch (\Exception $e) {
            $schedule->setErrorFile($e->getFile());
            $schedule->setErrorLine($e->getLine());
            $this->_taskHelper->setTrace($schedule);
            $this->processError($schedule, $e);
            // <CRONSCHEDULER>
            $this->eventManager->dispatch('cronscheduler_task_failed', ['task' => $schedule]);
            // </CRONSCHEDULER>
        } finally {
            $this->lockManager->unlock($lockName);
        }
    }

    /**
     * Process error messages.
     *
     * @param Schedule $schedule
     * @param \Exception $exception
     * @return void
     */
    private function processError(Schedule $schedule, \Exception $exception)
    {
        $schedule->setMessages($exception->getMessage());
        if ($schedule->getStatus() === Schedule::STATUS_ERROR) {
            $this->logger->critical($exception);
        }
        if ($schedule->getStatus() === Schedule::STATUS_MISSED
            && $this->state->getMode() === State::MODE_DEVELOPER
        ) {
            $this->logger->info($schedule->getMessages());
        }
    }

    /**
     * Clean up schedule
     *
     * @param mixed $where
     * @return int
     */
    private function cleanup($where = ''): int
    {
        /** @var \Magento\Cron\Model\ResourceModel\Schedule $scheduleResource */
        $scheduleResource = $this->_scheduleFactory->create()->getResource();

        return (int)$this->retrier->execute(
            function () use ($scheduleResource, $where) {
                return $scheduleResource->getConnection()->delete(
                    $scheduleResource->getTable('cron_schedule'),
                    $where
                );
            },
            $scheduleResource->getConnection()
        );
    }
}
