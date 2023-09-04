<?php
namespace Aheadworks\Blog\Model\Api\SearchCriteria\CollectionProcessor\JoinProcessor\Tag;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class PostsData
 * @package Aheadworks\Blog\Model\Api\SearchCriteria\CollectionProcessor\JoinProcessor\Tag
 */
class PostsData implements CustomJoinInterface
{
    /** Alias of table, that will be joined */
    const BLOG_POST_TAG_ALIAS = "bpt";
    const BLOG_POST_STORE_ALIAS = "bps";
    const BLOG_POST_ALIAS = "bp";

    /**
     * @param \Aheadworks\Blog\Model\ResourceModel\Tag\Collection $collection
     * @return bool
     */
    public function apply(AbstractDb $collection)
    {
        $success = false;

        $isNotApplied = !array_key_exists(
            self::BLOG_POST_TAG_ALIAS,
            $collection->getSelect()->getPart(\Magento\Framework\Db\Select::FROM)
        );

        if ($isNotApplied) {
            $collection->joinPostTables(
                self::BLOG_POST_TAG_ALIAS,
                self::BLOG_POST_STORE_ALIAS,
                self::BLOG_POST_ALIAS
            );
            $success = true;
        }

        return $success;
    }
}
