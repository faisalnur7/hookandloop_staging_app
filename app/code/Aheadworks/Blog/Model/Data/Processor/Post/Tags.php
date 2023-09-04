<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class Tags
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class Tags implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (!isset($data[PostInterface::TAG_NAMES])) {
            $data[PostInterface::TAG_NAMES] = [];
        }

        return $data;
    }
}
