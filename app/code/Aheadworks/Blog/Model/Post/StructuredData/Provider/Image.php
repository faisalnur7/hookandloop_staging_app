<?php
namespace Aheadworks\Blog\Model\Post\StructuredData\Provider;

use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;

/**
 * Class Image
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData\Provider
 */
class Image implements ProviderInterface
{
    /**
     * @var FeaturedImageInfo
     */
    protected $imageInfo;

    /**
     * @param FeaturedImageInfo $imageInfo
     */
    public function __construct(
        FeaturedImageInfo $imageInfo
    ) {
        $this->imageInfo = $imageInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        $data = [];

        $imageFile = $post->getFeaturedImageFile();
        $imageUrl = $this->imageInfo->getImageUrl($imageFile);
        if (!empty($imageFile) && !empty($imageUrl)) {
            $data["image"] = [
                "@type" => "ImageObject",
                "url" => $imageUrl,
            ];
        }

        return $data;
    }
}
