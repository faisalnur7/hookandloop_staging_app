<?php
namespace Aheadworks\Blog\Block\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\Template;
use Aheadworks\Blog\Block\Post\Listing as PostListing;

/**
 * Class PrevNext
 *
 * @method PostInterface getPost()
 * @method RelatedPost setPost(PostInterface $post)
 * @package Aheadworks\Blog\Block\Post
 */
class PrevNext extends Template implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Blog::post/prev_next.phtml';

    /**
     * @var Url
     */
    private $url;

    /**
     * @var PostListing
     */
    private $postListing;

    /**
     * @param TemplateContext $context
     * @param Listing $postListing
     * @param Url $url
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        PostListing $postListing,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->postListing = $postListing;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getPost()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Retrieve previous post
     *
     * @return PostInterface|false
     */
    public function getPrevPost()
    {
        $this->postListing->getSearchCriteriaBuilder()
            ->addFilter(PostInterface::PUBLISH_DATE, $this->getPost()->getPublishDate(), 'lteq')
            ->addFilter(PostInterface::ID, $this->getPost()->getId(), 'neq')
            ->setPageSize(1);
        $result = $this->postListing->getPosts();

        return reset($result);
    }

    /**
     * Retrieve next post
     *
     * @return PostInterface|false
     */
    public function getNextPost()
    {
        $this->postListing->getSearchCriteriaBuilder()
            ->addFilter(PostInterface::PUBLISH_DATE, $this->getPost()->getPublishDate(), 'gteq')
            ->addFilter(PostInterface::ID, $this->getPost()->getId(), 'neq');
        $result = $this->postListing->getPosts();

        return end($result);
    }

    /**
     * Retrieve post url
     *
     * @param PostInterface $post
     * @return string
     */
    public function getPostUrl(PostInterface $post)
    {
        return $this->url->getPostUrl($post);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [\Aheadworks\Blog\Model\Post::CACHE_TAG_LISTING];
    }
}
