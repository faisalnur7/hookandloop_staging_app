<?php
namespace Aheadworks\Blog\Model\Post\StructuredData\Provider;

use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface;

/**
 * Class Base
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData\Provider
 */
class Base implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        return [
            "@context" => "https://schema.org/",
            "@type" => "Article",
            "headline" => $post->getTitle(),
        ];
    }
}
