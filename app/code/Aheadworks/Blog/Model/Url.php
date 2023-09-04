<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;
use Magento\Framework\UrlInterface;
use Aheadworks\Blog\Model\Url\Canonical\Category as CanonicalCategory;

/**
 * Class Url
 * @package Aheadworks\Blog\Model
 */
class Url
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CanonicalCategory
     */
    private $canonicalCategory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Config $config
     * @param UrlInterface $urlBuilder
     * @param CanonicalCategory $canonicalCategory
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        CanonicalCategory $canonicalCategory
    ) {
        $this->config = $config;
        $this->canonicalCategory = $canonicalCategory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve url for the direct route
     *
     * @param $route
     * @return string
     */
    private function getDirectUrl($route)
    {
        return $this->urlBuilder->getDirectUrl($route);
    }

    /**
     * Retrieves blog home url
     *
     * @return string
     */
    public function getBlogHomeUrl()
    {
        return $this->getDirectUrl($this->config->getRouteToBlog());
    }

    /**
     * Retrieves authors url
     *
     * @return string
     */
    public function getAuthorsPageUrl()
    {
        return $this->getDirectUrl(
            $this->config->getRouteToBlog()
            . '/' . $this->config->getRouteToAuthors() . $this->config->getUrlSuffixForOtherPages()
        );
    }

    /**
     * Retrieves post url
     *
     * @param PostInterface $post
     * @param CategoryInterface|null $category
     * @return string
     */
    public function getPostUrl(PostInterface $post, CategoryInterface $category = null)
    {
        $parts = [$this->config->getRouteToBlog()];
        if ($category) {
            $parts[] = $category->getUrlKey();
        }
        $parts[] = $post->getUrlKey();

        return $this->getDirectUrl(
            implode('/', $parts) . $this->config->getPostUrlSuffix()
        );
    }

    /**
     * @param PostInterface $post
     * @param int|null $storeId
     * @return string
     */
    public function getPostRoute(PostInterface $post, $storeId = null)
    {
        return $this->config->getRouteToBlog($storeId)
            . '/' . $post->getUrlKey() . $this->config->getPostUrlSuffix($storeId);
    }

    /**
     * Retrieves category url
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryUrl(CategoryInterface $category)
    {
        return $this->getDirectUrl(
            $this->getCategoryRoute($category)
        );
    }

    /**
     * @param CategoryInterface $category
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryRoute(CategoryInterface $category, $storeId = null)
    {
        return $this->config->getRouteToBlog($storeId)
            . '/' . $category->getUrlKey() . $this->config->getUrlSuffixForOtherPages($storeId);
    }

    /**
     * Retrieve author url
     *
     * @param AuthorInterface $author
     * @return string
     */
    public function getAuthorUrl(AuthorInterface $author)
    {
        return $this->getDirectUrl(
            $this->getAuthorRoute($author)
        );
    }

    /**
     * Retrieve author route
     *
     * @param AuthorInterface $author
     * @param int|null $storeId
     * @return string
     */
    public function getAuthorRoute(AuthorInterface $author, $storeId = null)
    {
        return $this->config->getRouteToBlog($storeId)
            . '/' . $this->config->getRouteToAuthors($storeId)
            . '/' . $author->getUrlKey() . $this->config->getAuthorUrlSuffix($storeId);
    }

    /**
     * Retrieve all authors page route
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAuthorsPageRoute($storeId = null)
    {
        return $this->config->getRouteToBlog($storeId)
            . '/' . $this->config->getRouteToAuthors($storeId)
            . $this->config->getUrlSuffixForOtherPages($storeId);
    }

    /**
     * Retrieves search by tag url
     *
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        $tagUrlKey = $this->getSearchByTagUrlKey($tag);

        return $this->getDirectUrl(
            $this->config->getRouteToBlog()
            . '/' . PathBuilder::TAG_PAGE_KEY
            . '/' . $tagUrlKey
        );
    }

    /**
     * Retrieves search by tag url key
     *
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchByTagUrlKey($tag)
    {
        $tagName = $tag instanceof TagInterface ? $tag->getName() : $tag;

        return urlencode($tagName) . $this->config->getUrlSuffixForOtherPages();
    }

    /**
     * Retrieves tag by url key
     *
     * @param string $urlKey
     * @return string
     */
    public function getTagNameByUrlKey($urlKey)
    {
        $tagName = $urlKey;
        $urlSuffix = $this->config->getUrlSuffixForOtherPages();
        if (!empty($urlSuffix) && $urlSuffix !== '/') {
            $tagName = mb_substr($tagName, 0, mb_strpos($tagName, $urlSuffix));
        }

        return $tagName;
    }

    /**
     * Get canonical URL of post
     *
     * @param PostInterface $post
     * @return string
     */
    public function getCanonicalUrl(PostInterface $post)
    {
        if ($category = $this->canonicalCategory->getCanonicalCategory($post)) {
             return $this->getPostUrl($post, $category);
        }
        return $this->getPostUrl($post);
    }
}
