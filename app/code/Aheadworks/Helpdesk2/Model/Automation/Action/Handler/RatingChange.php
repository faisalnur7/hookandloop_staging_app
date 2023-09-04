<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RatingChange
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class RatingChange implements ActionInterface
{
    private const RATING_VALUE = '{{rating}}';

    /**
     * @var MessageManagement
     */
    private MessageManagement $messageManagement;

    /**
     * @param MessageManagement $messageManagement
     */
    public function __construct(
        MessageManagement $messageManagement
    ) {
        $this->messageManagement = $messageManagement;
    }

    /**
     * Run rating change action
     *
     * @param array $actionData
     * @param EventDataInterface $eventData
     * @throws LocalizedException
     * @return bool
     */
    public function run($actionData, $eventData): bool
    {
        $this->messageManagement->createAutomationMessage(
            $eventData->getTicket()->getEntityId(),
            '',
            $this->getCustomerRatingScoreMessage($eventData, $actionData),
            $eventData->getEventName()
        );

        return true;
    }

    /**
     * Retrieve Customer Rating Message
     *
     * @param $eventData
     * @param $actionData
     * @return string
     */
    public function getCustomerRatingScoreMessage($eventData, $actionData): string
    {
        if (strpos($actionData['value'], self::RATING_VALUE) !== false) {
            $message = str_replace(
                self::RATING_VALUE,
                $eventData->getTicket()->getCustomerRating(),
                $actionData['value']
            );
        } else {
            $message = $actionData['value'] . ' ' . $eventData->getTicket()->getCustomerRating();
        }

        return $message;
    }
}
