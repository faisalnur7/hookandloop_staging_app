<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Api\TicketStatusRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class TicketStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class TicketStatus implements ModifierInterface
{
    /**
     * @var TicketStatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * @param TicketStatusRepositoryInterface $statusRepository
     */
    public function __construct(
        TicketStatusRepositoryInterface $statusRepository
    ) {
        $this->statusRepository = $statusRepository;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $status = $this->statusRepository->get($eventData->getTicket()->getStatusId());
        $templateVariables[EmailVariables::TICKET_STATUS] = __($status->getLabel())->render();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
