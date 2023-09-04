<?php
namespace Aheadworks\Blog\Block\Product;

use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Post;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Blog\Model\Url;
use Aheadworks\Blog\Block\Post\Listing as PostListing;
use Magento\Framework\View\Element\Template;

/**
 * Class PostList
 *
 * @package Aheadworks\Blog\Block\Product
 */
class PostList extends Template implements IdentityInterface
{
    /**
     * Path to template file in theme
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::product/post/list.phtml';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var PostListing
     */
    private $postListing;

    /**
     * @param Context $context
     * @param Config $config
     * @param Url $url
     * @param PostListing $postListing
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Url $url,
        PostListing $postListing,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->url = $url;
        $this->postListing = $postListing;
        if ($this->config->isDisplayPostsOnProductPage()) {
            $this->addTitle();
        }
    }

    /**
     * Retrieve product posts
     *
     * @return array|null
     */
    public function getPosts()
    {
        if ($this->config->isDisplayPostsOnProductPage()) {
            if ($productId = $this->getRequest()->getParam('id')) {
                $this->postListing->getSearchCriteriaBuilder()->addFilter(
                    'product_id',
                    $productId
                );
            }
            return $this->postListing->getPosts();
        }
        return [];
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
     * Add title
     */
    public function addTitle()
    {
        $title = __('Blog Posts');

        if ($postsCount = count($this->getPosts())) {
            $title = __('Blog Posts (%1)', $postsCount);
        }
        $this->setData('title', $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [Post::CACHE_TAG_LISTING];
        foreach ($this->getPosts() as $post) {
            $identities = [Post::CACHE_TAG . '_' . $post->getId()];
        }

        return $identities;
    }
}
