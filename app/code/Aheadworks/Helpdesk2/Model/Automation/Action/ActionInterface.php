<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface ActionInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
interface ActionInterface
{
    /**
     * Run action
     *
     * @param array $actionData
     * @param EventDataInterface $eventData
     * @throws LocalizedException
     * @return bool
     */
    public function run($actionData, $eventData);
}
