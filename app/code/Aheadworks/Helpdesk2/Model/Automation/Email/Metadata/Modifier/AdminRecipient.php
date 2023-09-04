<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class AdminRecipient
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class AdminRecipient implements ModifierInterface
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
        $agent = $this->userRepository->getById($eventData->getTicket()->getAgentId());
        $emailMetadata->setRecipientName($agent->getUserName());
        $emailMetadata->setRecipientEmail($agent->getEmail());

        return $emailMetadata;
    }
}
