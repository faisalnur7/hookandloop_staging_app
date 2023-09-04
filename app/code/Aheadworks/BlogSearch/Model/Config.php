<?php
namespace Aheadworks\BlogSearch\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**
     * Configuration path search query min length
     */
    const XML_PATH_SEARCH_QUERY_MIN_LENGTH = 'aw_blog/aw_blog_search/search_query_min_length';

    /**
     * Configuration path search query max length
     */
    const XML_PATH_SEARCH_QUERY_MAX_LENGTH = 'aw_blog/aw_blog_search/search_query_max_length';

    /**
     * Configuration path to post title weight
     */
    const XML_PATH_POST_TITLE_WEIGHT = 'aw_blog/aw_blog_search/post_title_weight';

    /**
     * Configuration path to post content weight
     */
    const XML_PATH_POST_CONTENT_WEIGHT = 'aw_blog/aw_blog_search/post_content_weight';

    /**
     * Configuration path to post author weight
     */
    const XML_PATH_POST_AUTHOR_WEIGHT = 'aw_blog/aw_blog_search/post_author_weight';

    /**
     * Configuration path to post tags weight
     */
    const XML_PATH_POST_TAGS_WEIGHT = 'aw_blog/aw_blog_search/post_tags_weight';

    /**
     * Configuration path to post meta title weight
     */
    const XML_PATH_POST_META_TITLE_WEIGHT = 'aw_blog/aw_blog_search/post_meta_title_weight';

    /**
     * Configuration path to post meta keywords weight
     */
    const XML_PATH_POST_META_KEYWORDS_WEIGHT = 'aw_blog/aw_blog_search/post_meta_keywords_weight';

    /**
     * Configuration path to post meta description weight
     */
    const XML_PATH_POST_META_DESCRIPTION_WEIGHT = 'aw_blog/aw_blog_search/post_meta_description_weight';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get search query min length
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchQueryMinLength($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_QUERY_MIN_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get search query max length
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchQueryMaxLength($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_QUERY_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post title search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostTitleWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_TITLE_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post content search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostContentWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_CONTENT_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post author search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostAuthorWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_AUTHOR_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post tags search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostTagsWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_TAGS_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post meta title search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostMetaTitleWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_META_TITLE_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post meta keywords search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostMetaKeywordsWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_META_KEYWORDS_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get post meta description search weight
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPostMetaDescriptionWeight($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POST_META_DESCRIPTION_WEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
