<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Model\Source\Config\Related\BlockPosition;
use Aheadworks\Blog\Model\Source\Config\Seo\UrlType;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Blog config
 */
class Config
{
    /**
     * Configuration path to blog is enabled flag
     */
    const XML_PATH_ENABLED = 'aw_blog/general/enabled';

    /**
     * Configuration path to blog is navigation menu link enabled flag
     */
    const XML_PATH_NAVIGATION_MENU_LINK_ENABLED = 'aw_blog/general/navigation_menu_link_enabled';

    /**
     * Configuration path to route to blog
     */
    const XML_PATH_ROUTE_TO_BLOG = 'aw_blog/general/route_to_blog';

    /**
     * Configuration path to post image placeholder
     */
    const XML_PATH_POST_IMAGE_PLACEHOLDER = 'aw_blog/general/post_image_placeholder';

    /**
     * Configuration path to post image placeholder alt text
     */
    const XML_PATH_POST_IMAGE_PLACEHOLDER_ALT_TEXT = 'aw_blog/general/placeholder_alt_text';

    /**
     * Configuration path to blog title
     */
    const XML_PATH_BLOG_TITLE = 'aw_blog/general/blog_title';

    /**
     * Configuration path to route to authors page
     */
    const XML_PATH_ROUTE_TO_AUTHORS = 'aw_blog/general/route_to_authors';

    /**
     * Configuration path to number of posts per page
     */
    const XML_PATH_POSTS_PER_PAGE = 'aw_blog/general/posts_per_page';

    /**
     * Configuration path to quantity of related posts
     */
    const XML_PATH_QTY_OF_RELATED_POSTS = 'aw_blog/general/related_posts_qty';

    /**
     * Configuration path to blog is grid view enabled flag
     */
    const XML_PATH_IS_GRID_VIEW_ENABLED = 'aw_blog/general/is_grid_view_enabled';

    /**
     * Configuration path to blog grid view column count
     */
    const XML_PATH_GRID_VIEW_COLUMN_COUNT = 'aw_blog/general/grid_view_column_count';

    /**
     * Configuration path to blog post view default
     */
    const XML_PATH_POST_VIEW_DEFAULT = 'aw_blog/general/post_view_default';

    /**
     * Configuration path to positions of sharing buttons to display
     */
    const XML_PATH_DISPLAY_SHARING_AT = 'aw_blog/general/display_sharing_buttons_at';

    /**
     * Configuration path to comments are enabled flag
     */
    const XML_PATH_COMMENTS_ENABLED = 'aw_blog/general/comments_enabled';

    /**
     * Configuration path to are authors displayed
     */
    const XML_PATH_ARE_AUTHORS_DISPLAYED = 'aw_blog/general/are_authors_displayed';

    /**
     * Configuration path to number of recent posts to display
     */
    const XML_PATH_RECENT_POSTS = 'aw_blog/sidebar/recent_posts';

    /**
     * Configuration path to number of featured posts to display
     */
    const XML_PATH_FEATURED_POSTS_QTY = 'aw_blog/sidebar/featured_posts_qty';

    /**
     * Configuration path to featured posts position to display
     */
    const XML_PATH_FEATURED_POSTS_POSITION = 'aw_blog/sidebar/featured_posts_position';

    /**
     * Configuration path to number of most popular tags to display
     */
    const XML_PATH_POPULAR_TAGS = 'aw_blog/sidebar/popular_tags';

    /**
     * Configuration path to "highlight popular tags" flag
     */
    const XML_PATH_HIGHLIGHT_TAGS = 'aw_blog/sidebar/highlight_popular_tags';

    /**
     * Configuration path to sidebar CMS block ID
     */
    const XML_PATH_SIDEBAR_CMS_BLOCK = 'aw_blog/sidebar/cms_block';

    /**
     * Configuration path to sidebar Category Listing Enabled
     */
    const XML_PATH_SIDEBAR_CATEGORY_LISTING_ENABLED = 'aw_blog/sidebar/category_listing_enabled';

    /**
     * Configuration path to sidebar Category Listing Enabled
     */
    const XML_PATH_SIDEBAR_CATEGORY_LISTING_LIMIT = 'aw_blog/sidebar/category_listing_limit';

    /**
     * Configuration path to blog meta description
     */
    const XML_PATH_META_DESCRIPTION = 'aw_blog/seo/meta_description';

    /**
     * Configuration path to url type
     */
    const XML_PATH_SEO_URL_TYPE = 'aw_blog/seo/url_type';

    /**
     *  Configuration path to blog facebook application ID
     */
    const XML_PATH_FACEBOOK_APP_ID = 'aw_blog/general/facebook_app_id';

    /**
     *  Configuration path to twitter site
     */
    const XML_PATH_META_TWITTER_SITE = 'aw_blog/general/twitter_site';

    /**
     * Configuration path to blog change frequency
     */
    const XML_PATH_SITEMAP_CHANGEFREQ = 'sitemap/aw_blog/changefreq';

    /**
     * Configuration path to blog priority
     */
    const XML_PATH_SITEMAP_PRIORITY = 'sitemap/aw_blog/priority';

    /**
     * Configuration path to display blog posts tab on product page
     */
    const XML_PATH_RELATED_DISPLAY_POSTS_ON_PRODUCT_PAGE = 'aw_blog/related_products/display_posts_on_product_page';

    /**
     * Configuration path to display related products block on post page
     */
    const XML_PATH_RELATED_BLOCK_POSITION = 'aw_blog/related_products/block_position';

    /**
     * Configuration path to related products block layout
     */
    const XML_PATH_RELATED_BLOCK_LAYOUT = 'aw_blog/related_products/block_layout';

    /**
     * Configuration path to max products to display
     */
    const XML_PATH_RELATED_PRODUCTS_LIMIT = 'aw_blog/related_products/products_limit';

    /**
     * Configuration path to display "Add to Cart" button
     */
    const XML_PATH_RELATED_DISPLAY_ADD_TO_CART = 'aw_blog/related_products/display_add_to_cart';

    /**
     *  Configuration path to store name
     */
    const XML_PATH_STORE_INFORMATION_NAME = 'general/store_information/name';

    /**
     * Configuration path to post url suffix
     */
    const XML_PATH_SEO_POST_URL_SUFFIX = 'aw_blog/seo/post_url_suffix';

    /**
     * Configuration path to author url suffix
     */
    const XML_PATH_SEO_AUTHOR_URL_SUFFIX = 'aw_blog/seo/author_url_suffix';

    /**
     * Configuration path to url suffix for other pages
     */
    const XML_PATH_SEO_URL_SUFFIX_FOR_OTHER_PAGES = 'aw_blog/seo/url_suffix_for_other_pages';

    /**
     * Configuration path to save rewrites history
     */
    const XML_PATH_SEO_SAVE_REWRITES_HISTORY = 'aw_blog/seo/save_rewrites_history';

    /**
     * Configuration path to category canonical tag
     */
    const XML_PATH_SEO_CATEGORY_CANONICAL_TAG = 'aw_blog/seo/category_canonical_tag';

    /**
     * Configuration path to post canonical tag
     */
    const XML_PATH_SEO_POST_CANONICAL_TAG = 'aw_blog/seo/post_canonical_tag';

    /**
     * Configuration path to author canonical tag
     */
    const XML_PATH_SEO_AUTHOR_CANONICAL_TAG = 'aw_blog/seo/author_canonical_tag';

    /**
     * Configuration path to use blog page canonical tag
     */
    const XML_PATH_SEO_IS_USE_BLOG_PAGE_CANONICAL_TAG = 'aw_blog/seo/is_use_blog_page_canonical_tag';

    /**
     * Configuration path to title prefix
     */
    const XML_PATH_SEO_TITLE_PREFIX = 'aw_blog/seo/title_prefix';

    /**
     * Configuration path to title suffix
     */
    const XML_PATH_SEO_TITLE_SUFFIX = 'aw_blog/seo/title_suffix';

    /**
     * Configuration path to blog meta keywords
     */
    const XML_PATH_META_KEYWORDS = 'aw_blog/seo/meta_keywords';

    /**
     * Configuration path to meta tags enabled flag
     */
    const XML_PATH_SEO_ARE_META_TAGS_ENABLED = 'aw_blog/seo/are_meta_tags_enabled';

    /**
     * Configuration path to SEO organization name field
     */
    const XML_PATH_SEO_ORGANIZATION_NAME = 'aw_blog/seo/organization_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get website id
     * Workaround solution to fix Magento bug https://github.com/magento/magento2/issues/7943
     *
     * @param null $storeId
     * @return int
     */
    private function getWebsiteId($storeId = null)
    {
        $store = $this->storeManager->getStore($storeId);
        if ($store) {
            return $store->getWebsiteId();
        } else {
            return null;
        }
    }

    /**
     * Check if blog is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isBlogEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($storeId)
        );
    }

    /**
     * Retrieve File Path Post Image Placeholder
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPostImagePlaceholder($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POST_IMAGE_PLACEHOLDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve File Path Post Image Placeholder Alt Text
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPostImagePlaceholderAltText($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POST_IMAGE_PLACEHOLDER_ALT_TEXT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check is navigation menu link enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isMenuLinkEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NAVIGATION_MENU_LINK_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get route to blog
     *
     * @param int|null $storeId
     * @return string
     */
    public function getRouteToBlog($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_ROUTE_TO_BLOG,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($storeId)
        );
    }

    /**
     * Get route to authors
     *
     * @param int|null $storeId
     * @return string
     */
    public function getRouteToAuthors($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ROUTE_TO_AUTHORS,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($storeId)
        );
    }

    /**
     * Get blog title
     *
     * @return string
     */
    public function getBlogTitle()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BLOG_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get number of posts per page
     *
     * @return int
     */
    public function getNumPostsPerPage()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_POSTS_PER_PAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get quantity of related posts
     *
     * @return int
     */
    public function getQtyOfRelatedPosts()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_QTY_OF_RELATED_POSTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if grid view is enabled
     *
     * @return bool
     */
    public function isGridViewEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_GRID_VIEW_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get grid view column count
     *
     * @return int
     */
    public function getGridViewColumnCount(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_GRID_VIEW_COLUMN_COUNT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default post view
     *
     * @return string
     */
    public function getDefaultPostView(?int $storeId = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POST_VIEW_DEFAULT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get positions of sharing buttons to display
     *
     * @return array
     */
    public function getDisplaySharingAt()
    {
        return explode(
            ',',
            (string)$this->scopeConfig->getValue(self::XML_PATH_DISPLAY_SHARING_AT, ScopeInterface::SCOPE_WEBSITE)
        );
    }

    /**
     * Check if comments are enabled
     *
     * @return bool
     */
    public function isCommentsEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_COMMENTS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    /**
     * Check if authors are displayed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function areAuthorsDisplayed($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ARE_AUTHORS_DISPLAYED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get number of recent posts to display
     *
     * @return int
     */
    public function getNumRecentPosts()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_RECENT_POSTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get number of featured posts to display
     *
     * @param int $storeId
     * @return int
     */
    public function getNumFeaturedPosts($storeId)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_FEATURED_POSTS_QTY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get featured posts position to display
     *
     * @param int $storeId
     * @return string
     */
    public function getFeaturedPostsPosition($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FEATURED_POSTS_POSITION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get number of most popular tags to display
     *
     * @return int
     */
    public function getNumPopularTags()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_POPULAR_TAGS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if popular tags should be highlighted
     *
     * @return bool
     */
    public function isHighlightTags()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_HIGHLIGHT_TAGS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get sidebar CMS block ID
     *
     * @return int
     */
    public function getSidebarCmsBlockId()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_SIDEBAR_CMS_BLOCK, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get blog meta description
     *
     * @return string
     */
    public function getBlogMetaDescription()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_META_DESCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get blog change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapChangeFrequency($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get blog priority
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapPriority($storeId)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SITEMAP_PRIORITY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if display blog posts tab on product page
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDisplayPostsOnProductPage($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RELATED_DISPLAY_POSTS_ON_PRODUCT_PAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if disabled related products
     *
     * @param int|null $storeId
     * @return bool
     */
    public function areRelatedProductsDisabled($storeId = null)
    {
        return !$this->isDisplayPostsOnProductPage($storeId) &&
            $this->getRelatedBlockPosition($storeId) === BlockPosition::NOT_DISPLAY;
    }

    /**
     * Check if used of related products limit in posts (this method is used for reindexing)
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUseProductsLimitInPosts($storeId = null)
    {
        return !$this->isDisplayPostsOnProductPage($storeId)
            && $this->getRelatedBlockPosition($storeId) !== BlockPosition::NOT_DISPLAY;
    }

    /**
     * Get display related products block on post page
     *
     * @param int|null $storeId
     * @return string
     */
    public function getRelatedBlockPosition($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RELATED_BLOCK_POSITION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get display related products block layout
     *
     * @param int|null $storeId
     * @return string
     */
    public function getRelatedBlockLayout($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RELATED_BLOCK_LAYOUT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get max products to display
     *
     * @param int|null $storeId
     * @return int
     */
    public function getRelatedProductsLimit($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RELATED_PRODUCTS_LIMIT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get display "Add to Cart" button
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isRelatedDisplayAddToCart($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RELATED_DISPLAY_ADD_TO_CART,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get store name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getStoreName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STORE_INFORMATION_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get facebook application ID
     *
     * @param int|null $storeId
     * @return string
     */
    public function getFacebookAppId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FACEBOOK_APP_ID,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($storeId)
        );
    }

    /**
     * Get meta twitter site
     *
     * @param int|null $storeId
     * @return string
     */
    public function getMetaTwitterSite($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_TWITTER_SITE,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($storeId)
        );
    }

    /**
     * Check if category listing visible in sidebar
     *
     * @param int|null $storeId
     * @return int
     */
    public function isDisplaySidebarCategoryListing($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SIDEBAR_CATEGORY_LISTING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sidebar category listing limit
     *
     * @param int|null $storeId
     * @return int
     */
    public function getNumCategoriesToDisplay($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SIDEBAR_CATEGORY_LISTING_LIMIT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo url type
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSeoUrlType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_URL_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Checks if post url includes category
     *
     * @param int|null $storeId
     * @returns bool
     */
    public function isPostUrlIncludesCategory($storeId = null)
    {
        return $this->getSeoUrlType($storeId) == UrlType::URL_INC_CATEGORY;
    }

    /**
     * Get seo post url suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPostUrlSuffix($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_POST_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo author url suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAuthorUrlSuffix($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_AUTHOR_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get SEO URL suffix for categories, authors page, blog home page and search by tag page
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUrlSuffixForOtherPages($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_URL_SUFFIX_FOR_OTHER_PAGES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo save rewrites history
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSaveRewritesHistory($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_SAVE_REWRITES_HISTORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo category canonical tag
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_CATEGORY_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo post canonical tag
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPostCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_POST_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo author canonical tag
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAuthorCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_AUTHOR_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if use on blog page canonical tag
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUseBlogPageCanonicalTag($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_IS_USE_BLOG_PAGE_CANONICAL_TAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo title prefix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitlePrefix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_TITLE_PREFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seo title suffix
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitleSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_TITLE_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get blog meta keywords
     *
     * @param int|null $storeId
     * @return string
     */
    public function getBlogMetaKeywords($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check are meta tags enabled
     *
     * @return bool
     */
    public function areMetaTagsEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_ARE_META_TAGS_ENABLED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * Retrieve organization name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOrganizationName($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEO_ORGANIZATION_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
