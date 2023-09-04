<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Tag
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Tag extends AbstractModel implements TagInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
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
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Helpdesk2\Api\Data\TagExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
