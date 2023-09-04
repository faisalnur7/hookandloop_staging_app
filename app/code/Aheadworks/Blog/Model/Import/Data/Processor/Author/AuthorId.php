<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Author;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\Post\Author\Resolver as AuthorResolver;

/**
 * Class AuthorId
 */
class AuthorId implements ProcessorInterface
{
    const FULL_NAME = 'fullname';

    /**
     * @var AuthorResolver
     */
    private $authorResolver;

    /**
     * AuthorId constructor.
     * @param AuthorResolver $authorResolver
     */
    public function __construct(
        AuthorResolver $authorResolver
    ) {
        $this->authorResolver = $authorResolver;
    }

    /**
     * @inheritDoc
     */
    public function process($data)
    {
        try {
            $data[self::FULL_NAME] = $data[AuthorInterface::FIRSTNAME] . ' ' . $data[AuthorInterface::LASTNAME];
            $data[AuthorInterface::ID] = $this->authorResolver->resolveId($data, self::FULL_NAME);
        } catch (\Error $e) {
            $data[AuthorInterface::ID] = null;
        }

        return $data;
    }
}