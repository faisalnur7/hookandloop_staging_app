<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Blog\Model\ResourceModel\Validator\UrlKeyIsUnique;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Validator\NotEmpty as NotEmptyValidator;

/**
 * Category model
 *
 * @method ResourceCategory getResource()
 *
 * @package Aheadworks\Blog\Model
 */
class Category extends AbstractModel implements CategoryInterface, IdentityInterface
{
    /**
     * Blog category cache tag
     */
    const CACHE_TAG = 'aw_blog_category';

    /**
     * Blog category sidebar cache tag
     */
    const CACHE_TAG_CATEGORY_SIDEBAR = 'aw_blog_category_sidebar';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var UrlKeyIsUnique
     */
    private $urlKeyIsUnique;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param UrlKeyIsUnique $urlKeyIsUnique
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
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
        $this->urlKeyIsUnique = $urlKeyIsUnique;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceCategory::class);
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
    public function getName()
    {
        return $this->getData(self::NAME);
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
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
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
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
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
        return $this->getData(self::PATH);
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
        return $this->getData(self::IMAGE_FILE_NAME);
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
        return $this->getData(self::IMAGE_TITLE);
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
        return $this->getData(self::IMAGE_ALT);
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
        return $this->getData(self::IS_DESCRIPTION_ENABLED);
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
        return $this->getData(self::DESCRIPTION);
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
        return $this->getData(self::DISPLAY_MODE);
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
        return $this->getData(self::CMS_BLOCK_ID);
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
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [self::CACHE_TAG . '_' . $this->getId()];
        if ($this->_appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $identities[] = self::CACHE_TAG;
        }
        return $identities;
    }

    /**
     * {@inheritdoc}
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

        $nameNotEmpty = new NotEmptyValidator();
        $nameNotEmpty->setMessage(__('Name is required.'), NotEmptyValidator::IS_EMPTY);
        $validator->addRule($nameNotEmpty, self::NAME);

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
     * Load category by url key
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
