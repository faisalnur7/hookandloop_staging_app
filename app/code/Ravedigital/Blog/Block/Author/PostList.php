<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Blog
 * @version    2.8.1
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Ravedigital\Blog\Block\Author;

use Aheadworks\Blog\Model\Post;
use Magento\Framework\DataObject\IdentityInterface;
use Ravedigital\Blog\Block\PostList as ParentPostList;

/**
 * List of posts block
 * @package Aheadworks\Blog\Block
 */
class PostList extends ParentPostList implements IdentityInterface
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
