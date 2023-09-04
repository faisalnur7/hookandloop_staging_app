<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

/**
 * Class AdminTypeMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class AdminTypeMessage implements ProcessorInterface
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
        try {
            $author = $this->authorResolver->resolveAgent();
            $data[MessageInterface::TYPE] = MessageType::ADMIN;
            $data[MessageInterface::CONTENT] = str_replace('=', '=3D', $data[MessageInterface::CONTENT]);
            $data[MessageInterface::AUTHOR_NAME] = $data[MessageInterface::AUTHOR_NAME] ?? $author->getName();
            $data[MessageInterface::AUTHOR_EMAIL] = $data[MessageInterface::AUTHOR_EMAIL] ?? $author->getEmail();
        } catch (\Exception $exception) {
            return $data;
        }

        return $data;
    }
}
