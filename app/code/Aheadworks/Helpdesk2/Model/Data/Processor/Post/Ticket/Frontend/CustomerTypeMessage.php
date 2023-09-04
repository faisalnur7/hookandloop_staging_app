<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Magento\Framework\Escaper;

/**
 * Class CustomerTypeMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend
 */
class CustomerTypeMessage implements ProcessorInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * CustomerTypeMessage constructor.
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $data[MessageInterface::TYPE] = MessageType::CUSTOMER;
        $data[MessageInterface::AUTHOR_NAME] =
            $data[MessageInterface::AUTHOR_NAME] ?? $data[TicketInterface::CUSTOMER_NAME];
        $data[MessageInterface::AUTHOR_EMAIL] =
            $data[MessageInterface::AUTHOR_EMAIL] ?? $data[TicketInterface::CUSTOMER_EMAIL];
        $data[MessageInterface::CONTENT] = $this->escaper->escapeHtml($data[MessageInterface::CONTENT]);

        return $data;
    }
}
