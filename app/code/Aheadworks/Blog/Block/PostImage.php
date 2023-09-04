<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class PostImage
 *
 * @method $this setPost(PostInterface $post)
 * @method PostInterface getPost()
 * @method $this setImgClass(string $class)
 * @method string getImgClass()
 */
class PostImage extends Template
{
    protected $_template = 'post_image.phtml';
}
