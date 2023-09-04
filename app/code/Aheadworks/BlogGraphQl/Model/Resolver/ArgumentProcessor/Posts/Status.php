<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class Status
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts
 */
class Status implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        $data['filter'][PostInterface::STATUS]['eq'] = PostStatus::PUBLICATION;
        return $data;
    }
}
