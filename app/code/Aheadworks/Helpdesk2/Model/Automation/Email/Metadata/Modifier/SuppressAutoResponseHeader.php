<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\Header;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterfaceFactory;

/**
 * Class SuppressAutoResponseHeader
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class SuppressAutoResponseHeader implements ModifierInterface
{
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
            ->setName('X-Auto-Response-Suppress')
            ->setValue('OOF');
        $emailMetadata->addHeader($header);

        $header = $this->headerFactory->create();
        $header
            ->setName('Auto-Submitted')
            ->setValue('auto-replied');
        $emailMetadata->addHeader($header);

        return $emailMetadata;
    }
}
