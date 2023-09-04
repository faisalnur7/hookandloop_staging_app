<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Aheadworks\Blog\Model\Config as BlogConfig;

/**
 * Class Config
 * @package Aheadworks\BlogGraphQl\Model\Resolver
 */
class Config implements ResolverInterface
{
    /**
     * @var BlogConfig
     */
    private $blogConfig;

    /**
     * @param BlogConfig $blogConfig
     */
    public function __construct(BlogConfig $blogConfig)
    {
        $this->blogConfig = $blogConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $storeId = isset($args['storeId']) ? $args['storeId'] : null;
        $config = [
            // general
            'enabled' => $this->blogConfig->isBlogEnabled($storeId),
            'navigation_menu_link_enabled' => $this->blogConfig->isMenuLinkEnabled($storeId),
            'route_to_blog' => $this->blogConfig->getRouteToBlog($storeId),
            'route_to_authors' => $this->blogConfig->getRouteToAuthors($storeId),
            'blog_title' => $this->blogConfig->getBlogTitle(),
            'posts_per_page' => $this->blogConfig->getNumPostsPerPage(),
            'related_posts_qty' => $this->blogConfig->getQtyOfRelatedPosts(),
            'is_grid_view_enabled' => $this->blogConfig->isGridViewEnabled($storeId),
            'grid_view_column_count' => $this->blogConfig->getGridViewColumnCount($storeId),
            'post_view_default' => $this->blogConfig->getDefaultPostView($storeId),
            'display_sharing_buttons_at' => $this->blogConfig->getDisplaySharingAt(),
            'are_authors_displayed' => $this->blogConfig->areAuthorsDisplayed($storeId),
            // sidebar
            'comments_enabled' => $this->blogConfig->isCommentsEnabled(),
            'recent_posts' => $this->blogConfig->getNumRecentPosts(),
            'popular_tags' => $this->blogConfig->getNumPopularTags(),
            'featured_posts_qty' => $this->blogConfig->getNumFeaturedPosts($storeId),
            'featured_posts_position' => $this->blogConfig->getFeaturedPostsPosition($storeId),
            'highlight_popular_tags' => $this->blogConfig->isHighlightTags(),
            'cms_block' => $this->blogConfig->getSidebarCmsBlockId(),
            'category_listing_enabled' => $this->blogConfig->isDisplaySidebarCategoryListing($storeId),
            'category_listing_limit' => $this->blogConfig->getNumCategoriesToDisplay($storeId),
            // seo
            'areMetaTagsEnabled' => $this->blogConfig->areMetaTagsEnabled(),
            'meta_description' => $this->blogConfig->getBlogMetaDescription(),
            'url_type' => $this->blogConfig->getSeoUrlType($storeId),
            'post_url_suffix' => $this->blogConfig->getPostUrlSuffix($storeId),
            'author_url_suffix' => $this->blogConfig->getAuthorUrlSuffix($storeId),
            'title_prefix' => $this->blogConfig->getTitlePrefix($storeId),
            'title_suffix' => $this->blogConfig->getTitleSuffix($storeId),
            'url_suffix_for_other_pages' => $this->blogConfig->getUrlSuffixForOtherPages($storeId),
            // related products
            'display_posts_on_product_page' => $this->blogConfig->isDisplayPostsOnProductPage($storeId),
            'block_position' => $this->blogConfig->getRelatedBlockPosition($storeId),
            'block_layout' => $this->blogConfig->getRelatedBlockLayout($storeId),
            'products_limit' => $this->blogConfig->getRelatedProductsLimit($storeId),
            'display_add_to_cart' => $this->blogConfig->isRelatedDisplayAddToCart($storeId)
        ];

        return $config;
    }
}
