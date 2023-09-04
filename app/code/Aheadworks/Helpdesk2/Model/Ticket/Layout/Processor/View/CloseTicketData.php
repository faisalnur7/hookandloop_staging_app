<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View;

use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;

/**
 * Class CloseTicketData
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View
 */
class CloseTicketData implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlBuilder $urlBuilder
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare close ticket url
     *
     * @param array $jsLayout
     * @param ViewRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        $formDataProvider = 'components/aw_helpdesk2_config_provider';
        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => [
                    'close_ticket_url' => $this->urlBuilder->getTicketCloseLink(
                        $renderer->getTicket()->getExternalLink()
                    ),
                    'reopen_ticket_url' => $this->urlBuilder->getTicketReopenLink(
                        $renderer->getTicket()->getExternalLink()
                    ),
                    'is_ticket_closed' => $renderer->getTicket()->getStatusId() == TicketStatus::CLOSED,
                    'closed_ticket_notice' => $this->getClosedTicketNoticeHtml()
                ]
            ]
        );

        return $jsLayout;
    }

    /**
     * Get closed ticket notice HTML
     *
     * @return Phrase
     */
    private function getClosedTicketNoticeHtml()
    {
        $link = sprintf("<a href='%s'>%s</a>", $this->urlBuilder->getTicketCreateLink(), __('here'));
        return __('This ticket is closed. Click %1 to create a new one.', $link);
    }
}
