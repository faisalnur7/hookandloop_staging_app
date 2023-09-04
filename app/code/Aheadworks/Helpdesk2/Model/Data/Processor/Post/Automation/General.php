<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class General
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation
 */
class General implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (!isset($data[AutomationInterface::CONDITIONS])) {
            $data[AutomationInterface::CONDITIONS] = [];
        }

        if (!isset($data[AutomationInterface::ACTIONS])) {
            $data[AutomationInterface::ACTIONS] = [];
        }

        return $data;
    }
}
