<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class FeaturedImageMobileFile
 */
class FeaturedImageMobileFile implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        if (!isset($data[PostInterface::FEATURED_IMAGE_MOBILE_FILE])) {
            $data[PostInterface::FEATURED_IMAGE_MOBILE_FILE] = '';
        }

        return $data;
    }
}