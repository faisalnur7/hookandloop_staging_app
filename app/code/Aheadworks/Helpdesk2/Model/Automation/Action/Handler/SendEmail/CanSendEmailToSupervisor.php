<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\Ticket\Search\EscalationChecker;

/**
 * Class CanSendEmailToSupervisor
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail
 */
class CanSendEmailToSupervisor implements CanSendEmailCheckerInterface
{
    /**
     * @var EscalationChecker
     */
    private $escalationChecker;

    /**
     * @param EscalationChecker $escalationChecker
     */
    public function __construct(
        EscalationChecker $escalationChecker
    ) {
        $this->escalationChecker = $escalationChecker;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canSend($actionData, $eventData)
    {
        return $this->escalationChecker->isTicketCanBeEscalated();
    }
}
