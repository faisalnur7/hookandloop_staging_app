<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\DateResolver;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;

/**
 * Post resource model
 */
class Post extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     */
    const BLOG_POST_TABLE = 'aw_blog_post';
    const BLOG_POST_CATEGORY_TABLE = 'aw_blog_post_category';
    const BLOG_POST_STORE_TABLE = 'aw_blog_post_store';
    const BLOG_POST_TAG_TABLE = 'aw_blog_post_tag';
    /**#@-*/

    /**
     * @var DateResolver
     */
    private $dateResolver;

    /**
     * Post constructor.
     * @param Context $context
     * @param DateResolver $dateResolver
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateResolver $dateResolver,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->dateResolver = $dateResolver;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::BLOG_POST_TABLE, 'id');
    }

    /**
     * Load post by url key
     *
     * @param \Aheadworks\Blog\Model\Post $post
     * @param string $urlKey
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByUrlKey(\Aheadworks\Blog\Model\Post $post, $urlKey)
    {
        $connection = $this->getConnection();
        $bind = ['url_key' => $urlKey];
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(
                'url_key = :url_key'
            );

        $postId = $connection->fetchOne($select, $bind);
        if ($postId) {
            $this->load($post, $postId);
        } else {
            $post->setData([]);
        }

        return $this;
    }

    /**
     * Update posts status
     *
     * @param int[] $postIds
     * @param string $status
     * @return $this
     */
    public function updateStatus(array $postIds, string $status)
    {
        $table = $this->getTable(self::BLOG_POST_TABLE);
        $bind = [PostInterface::STATUS => $status];
        $where = [PostInterface::ID . ' IN (?)' => $postIds];
        $this->getConnection()->update($table, $bind, $where);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $post)
    {
        $post->setUpdatedAt($this->dateResolver->getCurrentDatetimeInDbFormat());

        return parent::_beforeSave($post);
    }
}
