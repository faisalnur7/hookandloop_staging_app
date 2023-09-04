<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail;

use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface CanSendEmailCheckerInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
interface CanSendEmailCheckerInterface
{
    /**
     * Can send email
     *
     * @param array $actionData
     * @param EventDataInterface $eventData
     * @return bool
     */
    public function canSend($actionData, $eventData);
}
