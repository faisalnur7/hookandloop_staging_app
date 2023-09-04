<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Api\Data\PostPreviewInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class PostPreview
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class PostPreview extends AbstractDb
{
    /**#@+
     * Constants defined for table names
     */
    const BLOG_POST_PREVIEW_TABLE = 'aw_blog_post_preview';
    /**#@-*/

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::BLOG_POST_PREVIEW_TABLE, PostPreviewInterface::ID);
    }
}
