<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags;

use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class PostsStatus
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags
 */
class PostsStatus implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        $data['filter']['posts_status']['eq'] = PostStatus::PUBLICATION;
        return $data;
    }
}
