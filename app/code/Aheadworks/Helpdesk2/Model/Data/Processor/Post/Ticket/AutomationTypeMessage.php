<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

/**
 * Class AutomationTypeMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class AutomationTypeMessage implements ProcessorInterface
{
    /**
     * @var Resolver
     */
    private $authorResolver;

    /**
     * @param Resolver $authorResolver
     */
    public function __construct(
        Resolver $authorResolver
    ) {
        $this->authorResolver = $authorResolver;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $author = $this->authorResolver->resolveAutomation();

        $data[MessageInterface::TYPE] = MessageType::ADMIN;
        $data[MessageInterface::AUTHOR_NAME] =
            $data[MessageInterface::AUTHOR_NAME] ?? $author->getName();
        $data[MessageInterface::AUTHOR_EMAIL] =
            $data[MessageInterface::AUTHOR_EMAIL] ?? $author->getEmail();

        return $data;
    }
}
