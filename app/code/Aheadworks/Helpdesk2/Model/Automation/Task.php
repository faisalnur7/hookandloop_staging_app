<?php
namespace Aheadworks\Helpdesk2\Model\Automation;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Task as TaskResourceModel;

/**
 * Class Task
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
class Task extends AbstractModel implements TaskInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(TaskResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getAutomationId()
    {
        return $this->getData(self::AUTOMATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAutomationId($automationId)
    {
        return $this->setData(self::AUTOMATION_ID, $automationId);
    }

    /**
     * @inheritdoc
     */
    public function getTicketId()
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * @inheritdoc
     */
    public function getActionType()
    {
        return $this->getData(self::ACTION_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setActionType($actionType)
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    /**
     * @inheritdoc
     */
    public function getActionData()
    {
        return $this->getData(self::ACTION_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setActionData($actionData)
    {
        return $this->setData(self::ACTION_DATA, $actionData);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
