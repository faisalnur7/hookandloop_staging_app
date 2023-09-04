<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class ShortContent
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class ShortContent implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (!isset($data['has_short_content']) || !$data['has_short_content']) {
            $data[PostInterface::SHORT_CONTENT] = '';
        }

        return $data;
    }
}
