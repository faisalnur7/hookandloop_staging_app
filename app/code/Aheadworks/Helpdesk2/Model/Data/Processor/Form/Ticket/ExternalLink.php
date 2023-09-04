<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class ExternalLink
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class ExternalLink implements ProcessorInterface
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
    public function prepareEntityData($data)
    {
        $data[TicketInterface::EXTERNAL_LINK] = $this->urlBuilder->getTicketExternalLink(
            $data[TicketInterface::EXTERNAL_LINK]
        );

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
