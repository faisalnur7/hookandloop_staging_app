<?php
namespace Aheadworks\Blog\Model\UrlRewrites;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Model\UrlRewrites\UrlConfigMetadata\UrlConfigMetadata as UrlConfigMetadataModel;

/**
 * Class PathBuilder
 * @package Aheadworks\Blog\Model\UrlRewrites
 */
class PathBuilder
{
    const TAG_PAGE_KEY = 'tag';
    const SEARCH_PAGE_KEY = 'search';

    /**
     * Builds blog home path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @return string
     */
    public function buildBlogHomePath($urlConfigMetadata)
    {
        return trim($urlConfigMetadata->getRouteToBlog(), '/');
    }

    /**
     * Builds blog authors page path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @return string
     */
    public function buildBlogAuthorsPath($urlConfigMetadata)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . $urlConfigMetadata->getRouteToAuthors()
            . $urlConfigMetadata->getUrlSuffixForOtherPages();

        return $path;
    }

    /**
     * Builds post path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param PostInterface $post
     * @return string
     */
    public function buildPostPath($urlConfigMetadata, $post)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . $post->getUrlKey()
            . $urlConfigMetadata->getPostUrlSuffix();

        return $path;
    }

    /**
     * Builds post with category path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param PostInterface $post
     * @param CategoryInterface $category
     * @return string
     */
    public function buildPostWithCategoryPath($urlConfigMetadata, $post, $category)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . $category->getUrlKey()
            . '/' . $post->getUrlKey()
            . $urlConfigMetadata->getPostUrlSuffix();

        return $path;
    }

    /**
     * Builds category path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param CategoryInterface $category
     * @return string
     */
    public function buildCategoryPath($urlConfigMetadata, $category)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . $category->getUrlKey()
            . $urlConfigMetadata->getUrlSuffixForOtherPages();

        return $path;
    }

    /**
     * Builds author path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param AuthorInterface $author
     * @return string
     */
    public function buildAuthorPath($urlConfigMetadata, $author)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . $urlConfigMetadata->getRouteToAuthors()
            . '/' . $author->getUrlKey()
            . $urlConfigMetadata->getAuthorUrlSuffix();

        return $path;
    }

    /**
     * Builds tag path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @param TagInterface $tag
     * @return string
     */
    public function buildTagPath($urlConfigMetadata, $tag)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . self::TAG_PAGE_KEY
            . '/' . urlencode($tag->getName())
            . $urlConfigMetadata->getUrlSuffixForOtherPages();

        return $path;
    }

    /**
     * Builds search path
     *
     * @param UrlConfigMetadataModel $urlConfigMetadata
     * @return string
     */
    public function buildSearchPath($urlConfigMetadata)
    {
        $path = $this->buildBlogHomePath($urlConfigMetadata)
            . '/' . self::SEARCH_PAGE_KEY;

        return trim($path, '/');
    }
}
