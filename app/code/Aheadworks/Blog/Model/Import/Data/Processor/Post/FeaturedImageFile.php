<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class FeaturedImageFile
 */
class FeaturedImageFile implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process($data)
    {
        if (!isset($data[PostInterface::FEATURED_IMAGE_FILE])) {
            $data[PostInterface::FEATURED_IMAGE_FILE] = '';
        }

        return $data;
    }
}