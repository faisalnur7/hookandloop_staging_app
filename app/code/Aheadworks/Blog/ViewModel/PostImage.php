<?php
namespace Aheadworks\Blog\ViewModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Post\Author\Resolver;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class PostImage
 */
class PostImage extends Post
{
    const DEFAULT_PLACEHOLDER_DIR = 'aw_blog/post/placeholder';

    /**
     * @var FeaturedImageInfo
     */
    private $imageInfo;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param FeaturedImageInfo $imageInfo
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param Resolver $authorResolver
     */
    public function __construct(
        FeaturedImageInfo $imageInfo,
        Config $config,
        StoreManagerInterface $storeManager,
        Resolver $authorResolver
    ) {
        parent::__construct($config, $storeManager, $authorResolver);
        $this->imageInfo = $imageInfo;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Get featured image url
     *
     * @param PostInterface $post
     * @return string
     */
    public function getFeaturedImageUrl(PostInterface $post)
    {
        return $this->imageInfo->getImageUrl($post->getFeaturedImageFile()) ?: $this->getPlaceHolderImageUrl();
    }

    /**
     * Retrieve placeholder image url
     *
     * @return bool|string
     */
    public function getPlaceHolderImageUrl()
    {
        return $this->imageInfo->getImageUrl($this->getPlaceholderImageFile());
    }

    /**
     * Retrieve placeholder image file
     *
     * @return string
     */
    public function getPlaceholderImageFile()
    {
        return self::DEFAULT_PLACEHOLDER_DIR . '/' . $this->config->getPostImagePlaceholder();
    }

    /**
     * Get featured image mobile url
     *
     * @param PostInterface $post
     * @return string
     */
    public function getFeaturedImageMobileUrl(PostInterface $post)
    {
        $imageFile = $post->getFeaturedImageMobileFile() ?: $post->getFeaturedImageFile();

        return $this->imageInfo->getImageUrl($imageFile) ?: $this->getPlaceHolderImageUrl();
    }

    /**
     * Retrieve Featured Image Alt
     *
     * @param PostInterface $post
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFeaturedImageAlt($post)
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $post->getFeaturedImageAlt() ?: $this->config->getPostImagePlaceholderAltText($storeId);
    }
}
