<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\CronScheduler\Block\Adminhtml\Task;

/**
 * Tasks Timeline block
 * @version 1.0.0
 */
class Timeline extends \Magento\Backend\Block\Template
{
    public $_datetime = null;
    public $_taskHelper = null;
    /**
     * @var \Wyomind\CronScheduler\Model\ResourceModel\Task\CollectionFactory
     */
    protected $_collectionFactory = null;
    /**
     * @var string
     */
    protected $_magentoVersion = "";
    public $jobHelper;
    public function __construct(
        \Wyomind\CronScheduler\Helper\Delegate $wyomind,
        \Magento\Backend\Block\Template\Context $context,
        /** @delegation off */
        \Wyomind\CronScheduler\Model\ResourceModel\Task\CollectionFactory $collectionFactory,
        /** @delegation off */
        array $data = []
    )
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_collectionFactory = $collectionFactory;
        $explodedVersion = explode("-", $this->productMetaData->getVersion());
        // in case of 2.2.0-dev
        $this->_magentoVersion = $explodedVersion[0];
        parent::__construct($context, $data);
        $this->setTemplate('task/timeline.phtml');
    }
    /**
     * Get the task view modal popup url
     * @return string
     */
    public function getViewUrl()
    {
        return $this->getUrl(\Wyomind\CronScheduler\Helper\Url::TASK_VIEW);
    }
    /**
     * Add the timezone offset to a datetime
     * @param string $datetime
     * @return string
     */
    private function addOffset($datetime)
    {
        if ($datetime == null) {
            return null;
        }
        if (version_compare($this->_magentoVersion, "2.2.0") >= 0) {
            return $this->_datetime->date("Y-m-d H:i:s", strtotime($datetime) + $this->_datetime->getGmtOffSet('seconds'));
        } else {
            return $datetime;
        }
    }
    /**
     * Get the system current date for javascript use
     * @return string
     */
    public function getCurrentJsDate()
    {
        $current = $this->_datetime->date('U') + $this->_datetime->getGmtOffSet('seconds');
        return "new Date(" . $this->_datetime->date("Y,", $current) . ($this->_datetime->date("m", $current) - 1) . $this->_datetime->date(",d,H,i,s", $current) . ")";
    }
    /**
     * Get the data to construct the timeline
     * @return array
     */
    public function getTimelineData()
    {
        $jobData = $this->jobHelper->getJobData();
        $data = [];
        $tasks = $this->_collectionFactory->create();
        $tasks->getSelect()->order('job_code');
        foreach ($tasks as $task) {
            if (isset($jobData[$task->getJobCode()])) {
                $start = $this->addOffset($task->getExecutedAt());
                $end = $this->addOffset($task->getFinishedAt());
                list($type, $inner) = $this->_taskHelper->getStatusRenderer($task->getStatus());
                $messages = (string) $task->getMessages();
                if (strlen($messages) > 200) {
                    $messages = substr($messages, 0, 200) . "...";
                }
                $messages = nl2br($messages);
                $tooltip = "<table class='task " . $type . "'>" . "<tr><td colspan='2'>" . $task->getJobCode() . "</td></tr>" . "<tr><td>" . __('Group') . "</td><td>" . $jobData[$task->getJobCode()]['group'] . "</td></tr>" . "<tr><td>" . __('Id') . "</td><td>" . $task->getId() . "</td></tr>" . "<tr><td>" . __('Status') . "</td><td>" . "<span class='grid-severity-" . $type . "'>" . $inner . "</span>" . "</td></tr>" . "<tr><td>" . __('Created at') . "</td><td>" . $this->addOffset($task->getCreatedAt()) . "</td></tr>" . "<tr><td>" . __('Scheduled at') . "</td><td>" . $this->addOffset($task->getScheduledAt()) . "</td></tr>" . "<tr><td>" . __('Executed at') . "</td><td>" . ($start != null ? $start : "") . "</td></tr>" . "<tr><td>" . __('Finished at') . "</td><td>" . ($end != null ? $end : "") . "</td></tr>";
                if ($messages != "") {
                    $tooltip .= "<tr><td>" . __('Messages') . "</td><td>" . $messages . "</td></tr>";
                }
                $tooltip .= "</table>";
                if ($start == null) {
                    $start = $this->addOffset($task->getScheduledAt());
                    $end = $this->addOffset($task->getScheduledAt());
                }
                if ($task->getStatus() == \Magento\Cron\Model\Schedule::STATUS_RUNNING) {
                    $end = $this->addOffset($this->_datetime->date('Y-m-d H:i:s'));
                }
                if ($task->getStatus() == \Magento\Cron\Model\Schedule::STATUS_ERROR && $end == null) {
                    $end = $start;
                }
                $data[] = [$task->getJobCode(), $task->getStatus(), $tooltip, "new Date(" . $this->_datetime->date('Y,', $start) . ($this->_datetime->date('m', $start) - 1) . $this->_datetime->date(',d,H,i,s,0', $start) . ")", "new Date(" . $this->_datetime->date('Y,', $end) . ($this->_datetime->date('m', $end) - 1) . $this->_datetime->date(',d,H,i,s,0', $end) . ")", $task->getScheduleId()];
            }
        }
        return $data;
    }
}
