<?php
namespace Aheadworks\Helpdesk2\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;

/**
 * Class StorefrontLabel
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class StorefrontLabel extends AbstractExtensibleObject implements StorefrontLabelInterface
{
    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->_get(self::CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
