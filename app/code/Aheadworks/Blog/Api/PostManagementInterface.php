<?php
namespace Aheadworks\Blog\Api;

use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Post management service interface
 * @api
 */
interface PostManagementInterface
{
    /**
     * Retrieve previous post
     *
     * @param PostInterface $post
     * @param int $storeId
     * @param int $customerGroupId
     * @return \Aheadworks\Blog\Api\Data\PostInterface[]|null
     */
    public function getPrevPost($post, $storeId, $customerGroupId);

    /**
     * Retrieve next post
     *
     * @param PostInterface $post
     * @param int $storeId
     * @param int $customerGroupId
     * @return \Aheadworks\Blog\Api\Data\PostInterface[]|null
     */
    public function getNextPost($post, $storeId, $customerGroupId);

    /**
     * Retrieve related post
     *
     * @param PostInterface $post
     * @param int $storeId
     * @param int $customerGroupId
     * @return \Aheadworks\Blog\Api\Data\PostInterface[]|null
     */
    public function getRelatedPosts($post, $storeId, $customerGroupId);
}
