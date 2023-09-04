<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Model\User;
use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

/**
 * Class AgentName
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class AgentName implements ModifierInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $agentId = $eventData->getTicket()->getAgentId();
        $templateVariables[EmailVariables::AGENT_NAME] = $this->resolveAgentName($agentId);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }

    /**
     * Resolve agent name
     *
     * @param int $agentId
     * @return string
     */
    private function resolveAgentName($agentId)
    {
        if ($agentId == AgentList::NOT_ASSIGNED_VALUE) {
            $agentName = AgentList::getNotAssignedLabel();
        } else {
            try {
                /** @var User $user */
                $user = $this->userRepository->getById($agentId);
                $agentName = $user->getName();
            } catch (NoSuchEntityException $exception) {
                $agentName = '';
            }
        }

        return $agentName;
    }
}
