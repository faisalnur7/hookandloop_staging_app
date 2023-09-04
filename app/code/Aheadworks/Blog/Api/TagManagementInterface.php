<?php
namespace Aheadworks\Blog\Api;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;

/**
 * interface TagManagementInterface
 */
interface TagManagementInterface
{
    /**
     * Returns tags for post
     *
     * @param PostInterface $post
     * @return TagInterface[]
     */
   public function getPostTags(PostInterface $post);
}
