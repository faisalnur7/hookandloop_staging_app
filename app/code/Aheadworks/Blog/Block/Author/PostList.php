<?php
namespace Aheadworks\Blog\Block\Author;

use Aheadworks\Blog\Model\Post;
use Aheadworks\Blog\Block\PostList as ParentPostList;

/**
 * List of posts block
 * @package Aheadworks\Blog\Block
 */
class PostList extends ParentPostList
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->applyPagination();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [Post::CACHE_TAG_LISTING];
        foreach ($this->getPosts() as $post) {
            $identities = [Post::CACHE_TAG . '_' . $post->getId()];
        }

        return $identities;
    }
}
