<?php
namespace Aheadworks\Blog\Model;

/**
 * Class Flag
 *
 * @package Aheadworks\Blog\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**#@+
     * Constants for blog flags
     */
    const AW_BLOG_SCHEDULE_POST_LAST_EXEC_TIME = 'aw_blog_schedule_post_last_exec_time';
    const AW_BLOG_EXPIRED_POST_LAST_EXEC_TIME = 'aw_blog_expired_post_last_exec_time';
    /**#@-*/

    /**
     * Setter for flag code
     * @codeCoverageIgnore
     *
     * @param string $code
     * @return $this
     */
    public function setBlogFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
