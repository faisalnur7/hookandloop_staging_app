<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\ResourceModel\Post as ResourcePost;
use Aheadworks\Blog\Model\ResourceModel\Validator\UrlKeyIsUnique;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Aheadworks\Blog\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Blog\Model\Rule\ProductFactory;
use Aheadworks\Blog\Model\Rule\Product;
use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Framework\Validator\NotEmpty as NotEmptyValidator;

/**
 * Post model
 *
 * @method ResourcePost getResource()
 *
 * @package Aheadworks\Blog\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Post extends AbstractModel implements PostInterface, IdentityInterface
{
    /**
     * Blog post cache tag
     */
    const CACHE_TAG = 'aw_blog_post';

    /**
     * Blog post listing cache tag
     */
    const CACHE_TAG_LISTING = 'aw_blog_post_listing';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var UrlKeyIsUnique
     */
    private $urlKeyIsUnique;

    /**
     * @var ProductFactory
     */
    private $productRuleFactory;

    /**
     * @var Product
     */
    private $productRule;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $productRuleFactory
     * @param ConditionConverter $conditionConverter
     * @param UrlKeyIsUnique $urlKeyIsUnique
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productRuleFactory,
        ConditionConverter $conditionConverter,
        UrlKeyIsUnique $urlKeyIsUnique,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->productRuleFactory = $productRuleFactory;
        $this->conditionConverter = $conditionConverter;
        $this->urlKeyIsUnique = $urlKeyIsUnique;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourcePost::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent()
    {
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortContent($shortContent)
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->getData(self::AUTHOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        return $this->setData(self::AUTHOR, $author);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorId()
    {
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorId($authorId)
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishDate()
    {
        return $this->getData(self::PUBLISH_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishDate($publishDate)
    {
        return $this->setData(self::PUBLISH_DATE, $publishDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate(): ?string
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate($endDate): PostInterface
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAllowComments()
    {
        return (bool)$this->getData(self::IS_ALLOW_COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAllowComments($isAllowComments)
    {
        return $this->setData(self::IS_ALLOW_COMMENTS, $isAllowComments);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFeatured()
    {
        return (bool)$this->getData(self::IS_FEATURED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFeatured($isFeatured)
    {
        return $this->setData(self::IS_FEATURED, $isFeatured);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorDisplayMode()
    {
        return $this->getData(self::AUTHOR_DISPLAY_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorDisplayMode($authorDisplayMode)
    {
        return $this->setData(self::AUTHOR_DISPLAY_MODE, $authorDisplayMode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAreRelatedProductsEnabled()
    {
        return $this->getData(self::ARE_RELATED_PRODUCTS_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAreRelatedProductsEnabled($flag)
    {
        return $this->setData(self::ARE_RELATED_PRODUCTS_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAuthorBadgeEnabled()
    {
        return $this->getData(self::IS_AUTHOR_BADGE_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAuthorBadgeEnabled($flag)
    {
        return $this->setData(self::IS_AUTHOR_BADGE_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->getData(self::CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($categoryIds)
    {
        return $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getTagIds()
    {
        return $this->getData(self::TAG_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTagIds($tagIds)
    {
        return $this->setData(self::TAG_IDS, $tagIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalCategoryId()
    {
        return $this->getData(self::CANONICAL_CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonicalCategoryId($canonicalCategoryId)
    {
        return $this->setData(self::CANONICAL_CATEGORY_ID, $canonicalCategoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTagNames()
    {
        return $this->getData(self::TAG_NAMES);
    }

    /**
     * {@inheritdoc}
     */
    public function setTagNames($tagNames)
    {
        return $this->setData(self::TAG_NAMES, $tagNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaPrefix()
    {
        return $this->getData(self::META_PREFIX);
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaPrefix($metaPrefix)
    {
        return $this->setData(self::META_PREFIX, $metaPrefix);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaSuffix()
    {
        return $this->getData(self::META_SUFFIX);
    }

    /**
     * {@inheritDoc}
     */
    public function setMetaSuffix($metaSuffix)
    {
        return $this->setData(self::META_SUFFIX, $metaSuffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCondition()
    {
        return $this->getData(self::PRODUCT_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCondition($productCondition)
    {
        return $this->setData(self::PRODUCT_CONDITION, $productCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImageFile()
    {
        return $this->getData(self::FEATURED_IMAGE_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImageFile($featuredImageFile)
    {
        return $this->setData(self::FEATURED_IMAGE_FILE, $featuredImageFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImageMobileFile()
    {
        return $this->getData(self::FEATURED_IMAGE_MOBILE_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImageMobileFile($featuredImageFile)
    {
        return $this->setData(self::FEATURED_IMAGE_MOBILE_FILE, $featuredImageFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImageTitle()
    {
        return $this->getData(self::FEATURED_IMAGE_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImageTitle($featuredImageTitle)
    {
        return $this->setData(self::FEATURED_IMAGE_TITLE, $featuredImageTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedImageAlt()
    {
        return $this->getData(self::FEATURED_IMAGE_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedImageAlt($featuredImageAlt)
    {
        return $this->setData(self::FEATURED_IMAGE_ALT, $featuredImageAlt);
    }

    /**
     * Get featured image link
     *
     * @return string|null
     */
    public function getFeaturedImageLink()
    {
        return $this->getData(self::FEATURED_IMAGE_LINK);
    }

    /**
     * Set featured image link
     *
     * @param string $featuredImageLink
     * @return $this
     */
    public function setFeaturedImageLink($featuredImageLink)
    {
        return $this->setData(self::FEATURED_IMAGE_LINK, $featuredImageLink);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTwitterSite()
    {
        return $this->getData(self::META_TWITTER_SITE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTwitterSite($metaTwitterSite)
    {
        return $this->setData(self::META_TWITTER_SITE, $metaTwitterSite);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\PostExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Get related product ids
     *
     * @return int[]|null
     */
    public function getRelatedProductIds()
    {
        return $this->getData(self::RELATED_PRODUCT_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setRelatedProductIds($relatedProductIds)
    {
        return $this->setData(self::RELATED_PRODUCT_IDS, $relatedProductIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedPostIds()
    {
        return $this->getData(self::RELATED_POST_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setRelatedPostIds($relatedPostIds)
    {
        return $this->setData(self::RELATED_POST_IDS, $relatedPostIds);
    }

    /**
     * Return product model with load conditions
     *
     * @return \Aheadworks\Blog\Model\Rule\Product
     */
    public function getProductRule()
    {
        if (!$this->productRule) {
            $this->productRule = $this->productRuleFactory->create();
            if ($this->getProductCondition()) {
                $conditionArray = $this->conditionConverter->dataModelToArray($this->getProductCondition());
                $this->productRule->setConditions([])
                    ->getConditions()
                    ->loadArray($conditionArray);
            } else {
                $this->productRule->setConditions([])
                    ->getConditions()
                    ->asArray();
            }
        }

        return $this->productRule;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [self::CACHE_TAG_LISTING, self::CACHE_TAG . '_' . $this->getId()];
        if (is_array($this->getCategoryIds())) {
            foreach ($this->getCategoryIds() as $categoryId) {
                $identities[] = \Aheadworks\Blog\Model\Category::CACHE_TAG . '_' . $categoryId;
            }
        }
        $tagNames = $this->getTagNames();
        if ($tagNames) {
            foreach ($tagNames as $tagName) {
                $identities[] = \Aheadworks\Blog\Model\Tag::CACHE_TAG . '_' . $tagName;
            }
        }
        if ($this->_appState->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND) {
            $identities[] = self::CACHE_TAG;
        }
        $relatedProductIds = $this->getRelatedProductIds();
        if (is_array($relatedProductIds) && count($relatedProductIds)) {
            foreach ($relatedProductIds as $relatedProductId) {
                $identities[] = CatalogProduct::CACHE_TAG . '_' . $relatedProductId;
            }
        }
        return $identities;
    }

    /**
     * @inheritdoc
     */
    public function validateBeforeSave()
    {
        parent::validateBeforeSave();
        if (!$this->urlKeyIsUnique->validate($this)) {
            throw new \Magento\Framework\Validator\Exception(
                __('This URL-Key is already assigned to another post, author or category.')
            );
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _getValidationRulesBeforeSave()
    {
        $validator = new \Magento\Framework\Validator\DataObject();

        $titleNotEmpty = new NotEmptyValidator();
        $titleNotEmpty->setMessage(__('Title is required.'), NotEmptyValidator::IS_EMPTY);
        $validator->addRule($titleNotEmpty, self::TITLE);

        $contentNotEmpty = new NotEmptyValidator();
        $contentNotEmpty->setMessage(__('Content is required.'), NotEmptyValidator::IS_EMPTY);
        $validator->addRule($contentNotEmpty, self::CONTENT);

        $urlKeyValid = new Validator\UrlKey();
        $urlKeyValid->setMessage(__('URL-Key is required.'), Validator\UrlKey::IS_EMPTY);
        $urlKeyValid->setMessage(__('URL-Key cannot consist only of numbers.'), Validator\UrlKey::IS_NUMBER);
        $urlKeyValid->setMessage(
            __('URL-Key cannot contain capital letters or disallowed symbols.'),
            Validator\UrlKey::CONTAINS_DISALLOWED_SYMBOLS
        );
        $validator->addRule($urlKeyValid, self::URL_KEY);

        $storesNotEmpty = new NotEmptyValidator();
        $storesNotEmpty->setMessage(__('Select store view.'), NotEmptyValidator::IS_EMPTY);
        $validator->addRule($storesNotEmpty, self::STORE_IDS);

        return $validator;
    }

    /**
     * Load post by url key
     *
     * @param   string $urlKey
     * @return  $this
     */
    public function loadByUrlKey($urlKey)
    {
        $this->_getResource()->loadByUrlKey($this, $urlKey);
        return $this;
    }
}
