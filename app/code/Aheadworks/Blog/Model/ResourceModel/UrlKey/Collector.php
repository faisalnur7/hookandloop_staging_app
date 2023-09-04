<?php
namespace Aheadworks\Blog\Model\ResourceModel\UrlKey;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\ResourceModel\Author as ResourceAuthor;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;

/**
 * Class Collector
 */
class Collector
{
    const URL_KEY_ALIAS = 'url_key';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Returns url keys for blog entities
     *
     * @param int[] $storeIds
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    public function getBlogUrlKeys(array $storeIds = [])
    {
        $connection = $this->getConnection();

        $unionSelect = $connection->select()->union(
            [
                $this->getSelectFromPosts($storeIds),
                $this->getSelectFromCategories($storeIds),
                $this->getSelectFromAuthors()
            ]
        );

        $select = $connection->select()
            ->from($unionSelect, self::URL_KEY_ALIAS);

        return $connection->fetchCol($select);
    }

    /**
     * Get select from posts table
     *
     * @param int[] $storeIds
     * @return Select
     */
    private function getSelectFromPosts(array $storeIds = [])
    {
        $postTable = $this->resourceConnection->getTableName(ResourcePost::BLOG_POST_TABLE);
        $postStoreTable = $this->resourceConnection->getTableName(ResourcePost::BLOG_POST_STORE_TABLE);
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['post_table' => $postTable], [self::URL_KEY_ALIAS => PostInterface::URL_KEY]);

        if (!empty($storeIds)) {
            $select->joinInner(
                ['post_store_table' => $postStoreTable],
                'post_store_table.post_id = post_table.' . PostInterface::ID,
                []
            )->where('post_store_table.store_id IN (?)', $storeIds);
        }

        return $select;
    }

    /**
     * Get select from categories table
     *
     * @param int[] $storeIds
     * @return Select
     */
    private function getSelectFromCategories(array $storeIds = [])
    {
        $categoryTable = $this->resourceConnection->getTableName(ResourceCategory::BLOG_CATEGORY_TABLE);
        $categoryStoreTable = $this->resourceConnection->getTableName(ResourceCategory::BLOG_CATEGORY_STORE_TABLE);
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['category_table' => $categoryTable], [self::URL_KEY_ALIAS => CategoryInterface::URL_KEY]);

        if (!empty($storeIds)) {
            $select->joinInner(
                ['category_store_table' => $categoryStoreTable],
                'category_store_table.category_id = category_table.' . CategoryInterface::ID,
                []
            )->where('category_store_table.store_id IN (?)', $storeIds);
        }

        return $select;

    }

    /**
     * Get select from authors table
     *
     * @return Select
     */
    private function getSelectFromAuthors()
    {
        $authorTable = $this->resourceConnection->getTableName(ResourceAuthor::BLOG_AUTHOR_TABLE);
        $connection = $this->getConnection();

        return $connection->select()
            ->from(['author_table' => $authorTable], [self::URL_KEY_ALIAS => AuthorInterface::URL_KEY]);

    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     */
    private function getConnection()
    {
        if (!isset($this->connection)) {
            $this->connection = $this->resourceConnection->getConnection();
        }
        return $this->connection;
    }
}
