<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class MetaTwitterSite
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class MetaTwitterSite implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data['use_default'])) {
            if (!empty($data['use_default'][PostInterface::META_TWITTER_SITE])) {
                $data[PostInterface::META_TWITTER_SITE] = '';
            }
        }

        return $data;
    }
}
