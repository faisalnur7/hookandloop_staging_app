<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Search\EscalationChecker;

/**
 * Class TicketEscalation
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View
 */
class TicketEscalation implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var EscalationChecker
     */
    private $escalationChecker;

    /**
     * @param ArrayManager $arrayManager
     * @param EscalationChecker $escalationChecker
     */
    public function __construct(
        ArrayManager $arrayManager,
        EscalationChecker $escalationChecker
    ) {
        $this->arrayManager = $arrayManager;
        $this->escalationChecker = $escalationChecker;
    }

    /**
     * Prepare ticket escalation data
     *
     * @param array $jsLayout
     * @param ViewRendererInterface $renderer
     * @return array
     * @throws NoSuchEntityException
     */
    public function process($jsLayout, $renderer)
    {
        $formDataProvider = 'components/aw_helpdesk2_escalate_form_data_provider';
        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => [
                    'key' => $renderer->getTicket()->getExternalLink()
                ]
            ]
        );

        $formDataProvider = 'components/aw_helpdesk2_config_provider';
        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => [
                    'is_escalation_enabled' => $this->escalationChecker->isTicketCanBeEscalated()
                ]
            ]
        );

        return $jsLayout;
    }
}
