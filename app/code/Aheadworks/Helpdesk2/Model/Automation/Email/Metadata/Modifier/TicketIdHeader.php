<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\Header;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterfaceFactory;

/**
 * Class TicketIdHeader
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class TicketIdHeader implements ModifierInterface
{
    const X_AW_HDU2_TICKET = 'X-Aw-Hdu2-Ticket';

    /**
     * @var HeaderInterfaceFactory
     */
    private $headerFactory;

    /**
     * @param HeaderInterfaceFactory $headerFactory
     */
    public function __construct(
        HeaderInterfaceFactory $headerFactory
    ) {
        $this->headerFactory = $headerFactory;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $ticket = $eventData->getTicket();
        /** @var Header $header */
        $header = $this->headerFactory->create();
        $header
            ->setName(self::X_AW_HDU2_TICKET)
            ->setValue($ticket->getUid());

        $emailMetadata->addHeader($header);

        return $emailMetadata;
    }
}
