<?php
namespace Aheadworks\Blog\Model\ResourceModel\Tag;

use Aheadworks\Blog\Api\Data\TagCloudItemInterface;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\Source\Post\Status;
use Aheadworks\Blog\Model\Tag;
use Aheadworks\Blog\Model\ResourceModel\Tag as ResourceTag;
use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Aheadworks\Blog\Model\ResourceModel\Tag
 */
class Collection extends \Aheadworks\Blog\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Tag::class, ResourceTag::class);
        $this->addFilterToMap('id', 'main_table.id');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'name');
    }

    /**
     * Join post tables
     *
     * @param $blogPostTagAlias
     * @param $blogPostStoreAlias
     * @param $blogPostAlias
     * @return $this
     */
    public function joinPostTables($blogPostTagAlias, $blogPostStoreAlias, $blogPostAlias)
    {
        $this->getSelect()
            ->joinLeft(
                [$blogPostTagAlias => $this->getTable(ResourcePost::BLOG_POST_TAG_TABLE)],
                "main_table.id = {$blogPostTagAlias}.tag_id",
                []
            )->joinLeft(
                [$blogPostStoreAlias => $this->getTable(ResourcePost::BLOG_POST_STORE_TABLE)],
                "{$blogPostTagAlias}.post_id = {$blogPostStoreAlias}.post_id",
                []
            )->joinLeft(
                [$blogPostAlias => $this->getTable(ResourcePost::BLOG_POST_TABLE)],
                "{$blogPostTagAlias}.post_id = {$blogPostAlias}.id",
                []
            )->group('main_table.id');;

        return $this;
    }

    /**
     * Dummy method, while tags have no store binding
     *
     * @param int|array $store
     * @param bool $withAdmin
     * @return \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}
