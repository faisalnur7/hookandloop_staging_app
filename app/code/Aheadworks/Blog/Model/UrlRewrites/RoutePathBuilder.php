<?php
namespace Aheadworks\Blog\Model\UrlRewrites;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;

/**
 * Class RoutePathBuilder
 * @package Aheadworks\Blog\Model\UrlRewrites
 */
class RoutePathBuilder
{
    /**
     * Builds blog home path
     *
     * @return string
     */
    public function buildBlogHomePath()
    {
        return 'aw_blog/index/index';
    }

    /**
     * Builds blog authors page path
     *
     * @return string
     */
    public function buildBlogAuthorsPath()
    {
        return 'aw_blog/author/list';
    }

    /**
     * Builds category path
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function buildCategoryPath($category)
    {
        $path = $this->buildRoutePathWithParams(
            'aw_blog/category/view',
            [
                'blog_category_id' => $category->getId()
            ]
        );

        return trim($path, '/');
    }

    /**
     * Builds post path
     *
     * @param PostInterface $post
     * @return string
     */
    public function buildPostPath($post)
    {
        $path = $this->buildRoutePathWithParams(
            'aw_blog/post/view',
            [
                'post_id' => $post->getId()
            ]
        );

        return trim($path, '/');
    }

    /**
     * Builds author path
     *
     * @param AuthorInterface $author
     * @return string
     */
    public function buildAuthorPath($author)
    {
        $path = $this->buildRoutePathWithParams(
            'aw_blog/author/view',
            [
                'author_id' => $author->getId()
            ]
        );

        return trim($path, '/');
    }

    /**
     * Builds tag path
     *
     * @param TagInterface $tag
     * @return string
     */
    public function buildTagPath($tag)
    {
        $path = $this->buildRoutePathWithParams(
            'aw_blog/index/index',
            [
                'tag_id' => $tag->getId()
            ]
        );

        return trim($path, '/');
    }

    /**
     * Builds route path with params
     *
     * @param string $routePath
     * @param array $routeParams associative array of params
     * @return string
     */
    private function buildRoutePathWithParams($routePath, $routeParams = [])
    {
        $paramsString = '';
        foreach ($routeParams as $paramName => $paramValue) {
            $paramsString .= $paramName . '/' . $paramValue . '/';
        }

        return $routePath . '/' . $paramsString;
    }

    /**
     * Builds search path
     *
     * @return string
     */
    public function buildSearchPath()
    {
        $path = $this->buildRoutePathWithParams(
            'aw_blog_search/search/index', []
        );

        return trim($path, '/');
    }
}
