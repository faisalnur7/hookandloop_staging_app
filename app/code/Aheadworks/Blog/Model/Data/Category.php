<?php
namespace Aheadworks\Blog\Model\Data;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Category data model
 * @codeCoverageIgnore
 */
class Category extends AbstractExtensibleObject implements CategoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
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
        return $this->_get(self::URL_KEY);
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
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
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
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
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
        return $this->_get(self::UPDATED_AT);
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
    public function getStoreIds()
    {
        return $this->_get(self::STORE_IDS);
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
    public function getMetaTitle()
    {
        return $this->_get(self::META_TITLE);
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
        return $this->_get(self::META_KEYWORDS);
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
        return $this->_get(self::META_DESCRIPTION);
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
        return $this->_get(self::META_PREFIX);
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
        return $this->_get(self::META_SUFFIX);
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
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->_get(self::PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        return $this->setData(self::PATH, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageFileName()
    {
        return $this->_get(self::IMAGE_FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageFileName($imageFileName)
    {
        return $this->setData(self::IMAGE_FILE_NAME, $imageFileName);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageTitle()
    {
        return $this->_get(self::IMAGE_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageTitle($imageTitle)
    {
        return $this->setData(self::IMAGE_TITLE, $imageTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageAlt()
    {
        return $this->_get(self::IMAGE_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageAlt($imageAlt)
    {
        return $this->setData(self::IMAGE_ALT, $imageAlt);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDescriptionEnabled()
    {
        return $this->_get(self::IS_DESCRIPTION_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDescriptionEnabled($flag)
    {
        return $this->setData(self::IS_DESCRIPTION_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayMode()
    {
        return $this->_get(self::DISPLAY_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayMode($displayMode)
    {
        return $this->setData(self::DISPLAY_MODE, $displayMode);
    }

    /**
     * {@inheritdoc}
     */
    public function getCmsBlockId()
    {
        return $this->_get(self::CMS_BLOCK_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCmsBlockId($cmsBlockId)
    {
        return $this->setData(self::CMS_BLOCK_ID, $cmsBlockId);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
