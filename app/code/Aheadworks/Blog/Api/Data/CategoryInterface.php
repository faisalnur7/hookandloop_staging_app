<?php
namespace Aheadworks\Blog\Api\Data;

/**
 * Category interface
 * @api
 */
interface CategoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const URL_KEY = 'url_key';
    const NAME = 'name';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STORE_IDS = 'store_ids';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const META_PREFIX = 'meta_prefix';
    const META_SUFFIX = 'meta_suffix';
    const PARENT_ID = 'parent_id';
    const PATH = 'path';
    const IMAGE_FILE_NAME = 'image_file_name';
    const IMAGE_TITLE = 'image_title';
    const IMAGE_ALT = 'image_alt';
    const IS_DESCRIPTION_ENABLED = 'is_description_enabled';
    const DESCRIPTION = 'description';
    const CMS_BLOCK_ID = 'cms_block_id';
    const DISPLAY_MODE = 'display_mode';
    /**#@-*/

    const ROOT_CATEGORY_ID = 0;

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get URL-Key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Set URL-Key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get store IDs
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set store IDs
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get display mode
     *
     * @return string|null
     */
    public function getDisplayMode();

    /**
     * Set display mode
     *
     * @param string $displayMode
     * @return $this
     */
    public function setDisplayMode($displayMode);

    /**
     * Get cms block id
     *
     * @return int
     */
    public function getCmsBlockId();

    /**
     * Set cms block id
     *
     * @param int $cmsBlockId
     * @return $this
     */
    public function setCmsBlockId($cmsBlockId);

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get meta prefix
     *
     * @return string|null
     */
    public function getMetaPrefix();

    /**
     * Set meta prefix
     *
     * @param string $metaPrefix
     * @return $this
     */
    public function setMetaPrefix($metaPrefix);

    /**
     * Get meta suffix
     *
     * @return string|null
     */
    public function getMetaSuffix();

    /**
     * Set meta suffix
     *
     * @param string $metaSuffix
     * @return $this
     */
    public function setMetaSuffix($metaSuffix);

    /**
     * Get parent ID
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set parent ID
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Get image file name
     *
     * @return string
     */
    public function getImageFileName();

    /**
     * Set image file name
     *
     * @param string $imageFileName
     * @return $this
     */
    public function setImageFileName($imageFileName);

    /**
     * Get image title
     *
     * @return string
     */
    public function getImageTitle();

    /**
     * Set  image title
     *
     * @param string $imageTitle
     * @return $this
     */
    public function setImageTitle($imageTitle);

    /**
     * Get image alt
     *
     * @return string
     */
    public function getImageAlt();

    /**
     * Set image alt
     *
     * @param string $imageAlt
     * @return $this
     */
    public function setImageAlt($imageAlt);

    /**
     * Get is description enabled
     *
     * @return bool
     */
    public function getIsDescriptionEnabled();

    /**
     * Set is description enabled
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsDescriptionEnabled($flag);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Blog\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes);
}
