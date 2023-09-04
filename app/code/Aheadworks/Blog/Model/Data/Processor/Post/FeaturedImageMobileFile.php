<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class FeaturedImageMobileFile
 */
class FeaturedImageMobileFile implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[PostInterface::FEATURED_IMAGE_MOBILE_FILE][0]['path'])) {
            $data[PostInterface::FEATURED_IMAGE_MOBILE_FILE] = $data[PostInterface::FEATURED_IMAGE_MOBILE_FILE][0]['path'];
        } else {
            $data[PostInterface::FEATURED_IMAGE_MOBILE_FILE] = '';
        }

        return $data;
    }
}
