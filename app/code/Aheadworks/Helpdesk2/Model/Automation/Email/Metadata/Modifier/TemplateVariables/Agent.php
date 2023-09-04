<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Agent implements ModifierInterface
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
        $templateVariables[EmailVariables::AGENT] = $this->userRepository->getById($agentId);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
