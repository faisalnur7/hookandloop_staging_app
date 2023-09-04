<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Status as StatusResourceModel;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Status extends AbstractModel implements TicketStatusInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(StatusResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getIsSystem()
    {
        return $this->getData(self::IS_SYSTEM);
    }

    /**
     * @inheritdoc
     */
    public function setIsSystem($isSystem)
    {
        return $this->setData(self::IS_SYSTEM, $isSystem);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketStatusExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
