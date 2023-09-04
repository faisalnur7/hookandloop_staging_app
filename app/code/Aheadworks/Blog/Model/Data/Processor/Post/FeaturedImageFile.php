<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class FeaturedImageFile
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class FeaturedImageFile implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[PostInterface::FEATURED_IMAGE_FILE][0]['path'])) {
            $data[PostInterface::FEATURED_IMAGE_FILE] = $data[PostInterface::FEATURED_IMAGE_FILE][0]['path'];
        } else {
            $data[PostInterface::FEATURED_IMAGE_FILE] = '';
        }

        return $data;
    }
}
