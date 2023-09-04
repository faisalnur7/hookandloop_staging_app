<?php
namespace Aheadworks\Blog\ViewModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Author\Resolver;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Post
 */
class Post implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Resolver
     */
    private $authorResolver;

    /**
     * Post constructor.
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        Resolver $authorResolver
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->authorResolver = $authorResolver;
    }

    /**
     * Retrieve Is Author Displayed
     *
     * @return bool
     */
    public function getIsAuthorDisplayed($post)
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->authorResolver->resolveToDisplayAuthor($post, $storeId);
    }

    /**
     * Render Author Badge Html
     *
     * @param AbstractBlock $block
     * @param PostInterface $post
     * @return mixed
     */
    public function renderAuthorBadgeHtml($block, $post)
    {
        $authorBadgeBlock = $block->getChildBlock('aw_blog_post.author_badge');
        $author = $post->getAuthor();

        return $authorBadgeBlock ? $authorBadgeBlock->setAuthor($author)->toHtml() : '';
    }

    /**
     * Check if need to display author badge block for current post
     *
     * @param PostInterface $post
     * @return bool
     */
    public function isNeedToDisplayAuthorBadgeBlock($post)
    {
        return $post->getIsAuthorBadgeEnabled();
    }

    /**
     * Retrieves featured image html
     *
     * @param AbstractBlock $block
     * @param string $blockAlias
     * @param PostInterface|null $post
     * @return string
     */
    public function getFeaturedImageHtml($block, string $blockAlias, $post = null)
    {
        $html = '';

        /** @var \Aheadworks\Blog\Block\PostImage $imageBlock */
        $imageBlock = $block->getChildBlock($blockAlias);
        if ($imageBlock) {
            $html = $imageBlock
                ->setPost($post ?: $block->getPost())
                ->toHtml();
        }

        return $html;
    }

    /**
     * Check if featured image is loaded
     *
     * @param PostInterface $post
     * @return bool
     */
    public function isFeaturedImageLoaded($post)
    {
        return $post->getFeaturedImageFile() ? true : false;
    }

    /**
     * Check if placeholder image is loaded
     *
     * @return bool
     */
    public function isPlaceholderImageLoaded()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->config->getPostImagePlaceholder($storeId) ? true : false;
    }
}
