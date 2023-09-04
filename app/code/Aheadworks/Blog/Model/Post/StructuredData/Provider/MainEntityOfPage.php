<?php
namespace Aheadworks\Blog\Model\Post\StructuredData\Provider;

use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface;
use Aheadworks\Blog\Model\Url as BlogUrl;

/**
 * Class MainEntityOfPage
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData\Provider
 */
class MainEntityOfPage implements ProviderInterface
{
    /**
     * @var BlogUrl
     */
    private $blogUrl;

    /**
     * @param BlogUrl $blogUrl
     */
    public function __construct(
        BlogUrl $blogUrl
    ) {
        $this->blogUrl = $blogUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        return [
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $this->blogUrl->getCanonicalUrl($post),
            ]
        ];
    }
}
