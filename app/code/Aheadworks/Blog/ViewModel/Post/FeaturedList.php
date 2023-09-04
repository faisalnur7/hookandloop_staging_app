<?php
namespace Aheadworks\Blog\ViewModel\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Provider as PostProvider;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Featured
 */
class FeaturedList implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PostProvider
     */
    private $postProvider;

    /**
     * Featured constructor.
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        PostProvider $postProvider
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->postProvider = $postProvider;
    }

    /**
     * Is Need Render Html
     *
     * @param AbstractBlock $block
     * @param PostInterface[]|array $posts
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isNeedRenderHtml($block, $posts)
    {
        return $posts
            && $this->getPosition() === $block->getPosition()
            && $this->getConfigQtyFeaturedPosts() > 0;
    }

    /**
     * Render Featured Posts Block
     *
     * @param AbstractBlock $block
     * @return string
     */
    public function renderFeaturedPosts($block, $posts)
    {
        $html = '';

        if ($this->isNeedRenderHtml($block, $posts)) {
            $position = $block->getPosition();
            $featuredPostsRenderer = $block->getChildBlock('aw_blog_featured_posts.' . $position . '.renderer');

            if ($featuredPostsRenderer) {
                $featuredPostsRenderer->setPosts($posts);
                $featuredPostsRenderer->setPosition($position);
                $html = $featuredPostsRenderer->toHtml();
            }
        }

        return $html;
    }

    /**
     * Retrieves featured posts
     *
     * @return PostInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFeaturedPosts()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->postProvider->getFeaturedPosts($storeId, $this->getConfigQtyFeaturedPosts());
    }

    /**
     * Retrieves featured posts position
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPosition()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->config->getFeaturedPostsPosition($storeId);
    }

    /**
     * Retrieves number of featured posts
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigQtyFeaturedPosts()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->config->getNumFeaturedPosts($storeId);
    }
}