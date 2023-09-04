<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;

/**
 * Class CreateSystemMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class CreateSystemMessage implements ActionInterface
{
    /**
     * @var MessageManagement
     */
    private $messageManagement;
    
    /**
     * @param MessageManagement $messageManagement
     */
    public function __construct(
        MessageManagement $messageManagement
    ) {
        $this->messageManagement = $messageManagement;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $this->messageManagement->createAutomationMessage(
            $eventData->getTicket()->getEntityId(),
            '',
            $actionData['value'],
            $eventData->getEventName()
        );

        return true;
    }
}
