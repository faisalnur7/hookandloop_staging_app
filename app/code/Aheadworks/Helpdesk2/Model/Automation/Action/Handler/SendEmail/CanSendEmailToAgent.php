<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action;

/**
 * Class CanSendEmailToAgent
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail
 */
class CanSendEmailToAgent implements CanSendEmailCheckerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @param UserRepository $userRepository
     * @param AdminSession $adminSession
     */
    public function __construct(
        UserRepository $userRepository,
        AdminSession $adminSession
    ) {
        $this->userRepository = $userRepository;
        $this->adminSession = $adminSession;
    }

    /**
     * @inheritdoc
     */
    public function canSend($actionData, $eventData)
    {
        $agentId = $eventData->getTicket()->getAgentId();
        if (!$agentId) {
            return false;
        }

        try {
            $this->userRepository->getById($agentId);
            if ($eventData->getEventName() == Event::TICKET_ASSIGNED) {
                return $this->canSendForCurrentAdmin($agentId, $actionData);
            }
            return true;
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Can send email for current admin
     *
     * @param int $agentId
     * @param array $actionData
     * @return bool
     */
    private function canSendForCurrentAdmin($agentId, $actionData)
    {
        $isDisabledToSend = isset($actionData['config'])
            && isset($actionData['config'][Action::IS_EMAIL_DISABLED_FOR_SAME_AGENT])
            ? (bool)$actionData['config'][Action::IS_EMAIL_DISABLED_FOR_SAME_AGENT] : false;

        $adminUser = $this->adminSession->getUser();
        if ($adminUser && $isDisabledToSend && $adminUser->getId()) {
            return $adminUser->getId() != $agentId;
        }

        return true;
    }
}
