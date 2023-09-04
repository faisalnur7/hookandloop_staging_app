<?php
namespace Aheadworks\Blog\Model\Resolver;

use Aheadworks\Blog\Api\Data\AuthorInterface;

/**
 * Class AuthorName
 */
class AuthorName
{
    /**
     * Returns full author name
     *
     * @param AuthorInterface $author
     */
    public function getFullAuthorName($author)
    {
        return $author->getFirstname() . ' ' . $author->getLastname();
    }
}
