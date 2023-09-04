<?php
namespace Aheadworks\Blog\Model\Post\StructuredData\Provider;

use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface;
use Aheadworks\Blog\Model\Post\Author\Resolver as PostAuthorResolver;

/**
 * Class Author
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData\Provider
 */
class Author implements ProviderInterface
{
    /**
     * @var PostAuthorResolver
     */
    private $postAuthorResolver;

    /**
     * @param PostAuthorResolver $postAuthorResolver
     */
    public function __construct(
        PostAuthorResolver $postAuthorResolver
    ) {
        $this->postAuthorResolver = $postAuthorResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        $data = [];

        $authorFullName = $this->postAuthorResolver->getFullName($post);
        if (!empty($authorFullName)) {
            $data["author"] = [
                "@type" => "Person",
                "name" => $authorFullName,
            ];
        }

        return $data;
    }
}
