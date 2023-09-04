<?php
namespace Aheadworks\Blog\Model\ResourceModel\Post;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Post;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\Source\Post\CustomerGroups;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Magento\Framework\DB\Select;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\Blog\Model\ResourceModel\Indexer\ProductPost as ResourceProductPost;
use Aheadworks\Blog\Model\ResourceModel\Tag as ResourceTag;
use Aheadworks\Blog\Model\ResourceModel\Author as ResourceAuthor;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;
use Aheadworks\Blog\Api\Data\CategoryInterface as BlogCategoryInterface;

/**
 * Class Collection
 * @package Aheadworks\Blog\Model\ResourceModel\Post
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    const IS_NEED_TO_ATTACH_RELATED_PRODUCT_IDS = 'is_need_to_attach_related_product_ids';

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param EventManager $eventManager
     * @param DateTime $dateTime
     * @param MetadataPool $metadataPool
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        EventManager $eventManager,
        DateTime $dateTime,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->dateTime = $dateTime;
        $this->metadataPool = $metadataPool;

        $this->setFlag(self::IS_NEED_TO_ATTACH_RELATED_PRODUCT_IDS, true);

        $this->addFilterToMap('tag_name', 'tag_main_table.name');
        $this->addFilterToMap('category_name', 'category_main_table.name');
        $this->addFilterToMap('category_url_key', 'category_main_table.url_key');
        $this->addFilterToMap('author_url_key', 'author_main_table.url_key');
        $this->addFilterToMap('status', 'main_table.status');
        $this->addFilterToMap('url_key', 'main_table.url_key');
        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('tag_name', 'tag_main_table.name');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Post::class, ResourcePost::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachStores(ResourcePost::BLOG_POST_STORE_TABLE, 'id', 'post_id');
        $this->attachCategories();
        $this->attachTagIds();
        $this->attachTagNames();
        $this->attachAuthor();
        if ($this->getFlag(self::IS_NEED_TO_ATTACH_RELATED_PRODUCT_IDS)) {
            $this->attachRelatedProductIds();
        }
        return parent::_afterLoad();
    }

    /**
     *  Add category filter
     *
     * @param int|array $categories
     * @return $this
     */
    public function addCategoryFilter($categories)
    {
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable(ResourceCategory::BLOG_CATEGORY_TABLE), ['id']);
        foreach ($categories as $category) {
            $select->orWhere("`path` LIKE '" . $category . "/%' OR `path` LIKE '%/" . $category . "/%'");
        }
        $categoryIds = array_merge($categories, (array)$this->getConnection()->fetchCol($select));

        $this->addFilter('category_id', ['in' => $categoryIds], 'public');
        return $this;
    }

    /**
     * Add category url key filter
     *
     * @param string|array $categoryUrlKey
     * @return $this
     */
    public function addCategoryUrlKeyFilter($categoryUrlKey)
    {
        if (!is_array($categoryUrlKey)) {
            $categoryUrlKey = [$categoryUrlKey];
        }

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable(ResourceCategory::BLOG_CATEGORY_TABLE), ['id'])
            ->where(BlogCategoryInterface::URL_KEY . ' IN (?)', $categoryUrlKey);
        $categoryIds = (array)$this->getConnection()->fetchCol($select);

        if (!empty($categoryIds)) {
            $this->addCategoryFilter($categoryIds);
        } else {
            $this->addFilter('category_url_key', ['in' => $categoryUrlKey], 'public');
        }

        return $this;
    }

    /**
     * Add category name filter
     *
     * @param string|array $categoryName
     * @return $this
     */
    public function addCategoryNameFilter($categoryName)
    {
        if (!is_array($categoryName)) {
            $categoryName = [$categoryName];
        }

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable(ResourceCategory::BLOG_CATEGORY_TABLE), ['id'])
            ->where(BlogCategoryInterface::NAME . ' IN (?)', $categoryName);
        $categoryIds = (array)$this->getConnection()->fetchCol($select);

        if (!empty($categoryIds)) {
            $this->addCategoryFilter($categoryIds);
        } else {
            $this->addFilter('category_name', ['in' => $categoryName], 'public');
        }

        return $this;
    }

    /**
     * Add author url key filter
     *
     * @param string|array $authorUrlKey
     * @return $this
     */
    public function addAuthorUrlKeyFilter($authorUrlKey)
    {
        if (!is_array($authorUrlKey)) {
            $authorUrlKey = [$authorUrlKey];
        }
        $this->addFilter('author_url_key', ['in' => $authorUrlKey], 'public');
        return $this;
    }

    /**
     * Add tag filter
     *
     * @param int|array $tag
     * @return $this
     */
    public function addTagFilter($tag)
    {
        if (!is_array($tag)) {
            $tag = [$tag];
        }
        $this->addFilter('tag_id', ['in' => $tag], 'public');
        return $this;
    }

    /**
     * Add tag name filter
     *
     * @param string|array $tagName
     * @return $this
     */
    public function addTagNameFilter($tagName)
    {
        if (!is_array($tagName)) {
            $tagName = [$tagName];
        }

        $this->addFilter('tag_name', ['in' => $tagName], 'public');
        return $this;
    }

    /**
     * Add related product filter
     *
     * @param int|array $product
     * @return $this
     */
    public function addRelatedProductFilter($product)
    {
        if (!is_array($product)) {
            $product = [$product];
        }
        $this->addFilter('product_id', ['in' => $product], 'public');
        return $this;
    }

    /**
     * Add customer groups filter. First of all it checks 'all groups'
     *    option and then specified one.
     *
     * @param string $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup)
    {
        $condition = [
            ['finset' => CustomerGroups::ALL_GROUPS],
            ['finset' => $customerGroup],
        ];
        $this->addFilter('customer_groups', $condition, 'public');
        return $this;
    }

    /**
     * Retrieve current loaded post ids considering limit and offset
     *
     * @return array
     */
    public function getCurrentLoadedIds()
    {
        $connection = $this->getConnection();
        $countQuery = $connection->select()->from($this->getSelect(), PostInterface::ID);
        return $connection->fetchCol($countQuery);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreLinkageTable(ResourcePost::BLOG_POST_STORE_TABLE, 'id', 'post_id');
        $this->joinCategoryLinkageTable();
        $this->joinCategoryTable();
        $this->joinTagLinkageTable();
        $this->joinTagTable();
        $this->joinAuthorTable();
        $this->joinRelatedProductLinkageTable();
        parent::_renderFiltersBefore();
    }

    /**
     * Join to category linkage table if category filter is applied
     *
     * @return void
     */
    private function joinCategoryLinkageTable()
    {
        if ($this->getFilter('category_id')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['category_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_CATEGORY_TABLE)],
                'main_table.id = category_linkage_table.post_id',
                []
            )
            ->group('main_table.id');
        }
    }

    /**
     * Join to category table if category filter is applied
     *
     * @return void
     */
    private function joinCategoryTable()
    {
        if ($this->getFilter('category_name') || $this->getFilter('category_url_key')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['category_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_CATEGORY_TABLE)],
                'main_table.id = category_linkage_table.post_id',
                []
            )->joinLeft(
                ['category_main_table' => $this->getTable(ResourceCategory::BLOG_CATEGORY_TABLE)],
                'category_linkage_table.category_id = category_main_table.id',
                []
            )
                ->group('main_table.id');
        }
    }

    /**
     * Join to author table if author filter is applied
     *
     * @return void
     */
    private function joinAuthorTable()
    {
        if ($this->getFilter('author_url_key')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['author_main_table' => $this->getTable(ResourceAuthor::BLOG_AUTHOR_TABLE)],
                'main_table.author_id = author_main_table.id',
                []
            )
                ->group('main_table.id');
        }
    }

    /**
     * Join to tag linkage table if tag filter is applied
     *
     * @return void
     */
    private function joinTagLinkageTable()
    {
        if ($this->getFilter('tag_id')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['tag_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_TAG_TABLE)],
                'main_table.id = tag_linkage_table.post_id',
                []
            )
            ->group('main_table.id');
        }
    }

    /**
     * Join to tag table if tag filter is applied
     *
     * @return void
     */
    private function joinTagTable()
    {
        if ($this->getFilter('tag_name')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['tag_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_TAG_TABLE)],
                'main_table.id = tag_linkage_table.post_id',
                []
            )->joinLeft(
                ['tag_main_table' => $this->getTable(ResourceTag::BLOG_TAG_TABLE)],
                'tag_linkage_table.tag_id = tag_main_table.id',
                []
            )
                ->group('main_table.id');
        }
    }

    /**
     * Join to product index linkage table if product filter is applied
     *
     * @return void
     */
    private function joinRelatedProductLinkageTable()
    {
        if ($this->getFilter('product_id')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['product_post_linkage_table' => $this->getTable(ResourceProductPost::BLOG_PRODUCT_POST_TABLE)],
                'main_table.id = product_post_linkage_table.post_id',
                []
            )
            ->group('main_table.id');
            if ($storeFilter = $this->getFilter('store_linkage_table.store_id')) {
                $select->where(
                    $this->_getConditionSql('product_post_linkage_table.store_id', $storeFilter->getValue()),
                    null,
                    Select::TYPE_CONDITION
                );
            }
        }
    }

    /**
     * Attach categories data to collection items
     *
     * @return void
     */
    private function attachCategories()
    {
        $postIds = $this->getColumnValues('id');
        if (count($postIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['category_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_CATEGORY_TABLE)])
                ->where('category_linkage_table.post_id IN (?)', $postIds);
            $result = $connection->fetchAll($select);
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $categoryIds = [];
                $postId = $item->getData('id');
                foreach ($result as $data) {
                    if ($data['post_id'] == $postId) {
                        $categoryIds[] = $data['category_id'];
                    }
                }
                $item->setData('category_ids', $categoryIds);
            }
        }
    }

    /**
     * Attach tag names to collection items
     *
     * @return void
     */
    private function attachTagIds()
    {
        $postIds = $this->getColumnValues('id');
        if (count($postIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['tags_table' => $this->getTable(ResourceTag::BLOG_TAG_TABLE)])
                ->joinLeft(
                    ['tag_post_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_TAG_TABLE)],
                    'tags_table.id = tag_post_linkage_table.tag_id',
                    ['post_id' => 'tag_post_linkage_table.post_id']
                )
                ->where('tag_post_linkage_table.post_id IN (?)', $postIds);
            /** @var \Magento\Framework\DataObject $item */
            $result = $connection->fetchAll($select);
            foreach ($this as $item) {
                $tagIds = [];
                $postId = $item->getData('id');
                foreach ($result as $data) {
                    if ($data['post_id'] == $postId) {
                        $tagIds[] = $data['id'];
                    }
                }
                $item->setData(PostInterface::TAG_IDS, $tagIds);
            }
        }
    }

    /**
     * Attach tag names to collection items
     *
     * @return void
     */
    private function attachTagNames()
    {
        $postIds = $this->getColumnValues('id');
        if (count($postIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['tags_table' => $this->getTable(ResourceTag::BLOG_TAG_TABLE)])
                ->joinLeft(
                    ['tag_post_linkage_table' => $this->getTable(ResourcePost::BLOG_POST_TAG_TABLE)],
                    'tags_table.id = tag_post_linkage_table.tag_id',
                    ['post_id' => 'tag_post_linkage_table.post_id']
                )
                ->where('tag_post_linkage_table.post_id IN (?)', $postIds);
            /** @var \Magento\Framework\DataObject $item */
            $result = $connection->fetchAll($select);
            foreach ($this as $item) {
                $tagNames = [];
                $postId = $item->getData('id');
                foreach ($result as $data) {
                    if ($data['post_id'] == $postId) {
                        $tagNames[] = $data['name'];
                    }
                }
                $item->setData('tag_names', $tagNames);
            }
        }
    }

    /**
     * Attach product ids data to collection items
     *
     * @return void
     */
    private function attachRelatedProductIds()
    {
        $postIds = $this->getColumnValues('id');
        if (count($postIds)) {
            $productLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['product_post_linkage_table' => $this->getTable(ResourceProductPost::BLOG_PRODUCT_POST_TABLE)])
                ->joinRight(
                    ['product_entity' => $this->getTable('catalog_product_entity')],
                    'product_post_linkage_table.product_id = product_entity.' . $productLinkField,
                    []
                )->where('product_post_linkage_table.post_id IN (?)', $postIds);
            if ($storeFilter = $this->getFilter('store_linkage_table.store_id')) {
                $select->where(
                    $this->_getConditionSql('product_post_linkage_table.store_id', $storeFilter->getValue()),
                    null,
                    Select::TYPE_CONDITION
                );
            }
            /** @var \Magento\Framework\DataObject $item */
            $result = $connection->fetchAll($select);
            foreach ($this as $item) {
                $productIds = [];
                $postId = $item->getData('id');
                foreach ($result as $data) {
                    if ($data['post_id'] == $postId) {
                        $productIds[] = $data['product_id'];
                    }
                }
                $item->setData('related_product_ids', $productIds);
            }
        }
    }

    /**
     * Attach author to collection items
     *
     * @return void
     */
    private function attachAuthor()
    {
        $authorIds = $this->getColumnValues(PostInterface::AUTHOR_ID);
        if (count($authorIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(
                    ['author_table' => $this->getTable(ResourceAuthor::BLOG_AUTHOR_TABLE)],
                    [
                        AuthorInterface::ID,
                        AuthorInterface::FIRSTNAME,
                        AuthorInterface::LASTNAME,
                        AuthorInterface::URL_KEY,
                        AuthorInterface::IMAGE_FILE,
                        AuthorInterface::SHORT_BIO,
                        AuthorInterface::TWITTER_ID
                    ]
                )
                ->where('author_table.id IN (?)', $authorIds);
            $result = $connection->fetchAll($select);

            /** @var Post $item */
            foreach ($this as $item) {
                $authorId = $item->getData(PostInterface::AUTHOR_ID);
                $authorData = [];
                foreach ($result as $data) {
                    if ($data[PostInterface::ID] == $authorId) {
                        $authorData = $data;
                        break;
                    }
                }
                $item->setData(PostInterface::AUTHOR, $authorData);
            }
        }
    }
}
