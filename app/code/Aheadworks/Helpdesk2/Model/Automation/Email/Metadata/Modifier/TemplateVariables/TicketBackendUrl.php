<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class TicketBackendUrl
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class TicketBackendUrl implements ModifierInterface
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(
        UrlBuilder $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $ticketId = $eventData->getTicket()->getEntityId();
        $templateVariables[EmailVariables::TICKET_BACKEND_URL] = $this->urlBuilder->getBackendTicketViewLink($ticketId);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
