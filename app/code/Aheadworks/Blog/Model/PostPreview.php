<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\PostPreviewInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Blog\Model\ResourceModel\PostPreview as ResourcePostPreview;

/**
 * Class PostPreview
 * @package Aheadworks\Blog\Model
 */
class PostPreview extends AbstractModel implements PostPreviewInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourcePostPreview::class);
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
    public function getPostPreviewData()
    {
        return $this->getData(self::POST_PREVIEW_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostPreviewData($data)
    {
        return $this->setData(self::POST_PREVIEW_DATA, $data);
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
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\PostPreviewExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
