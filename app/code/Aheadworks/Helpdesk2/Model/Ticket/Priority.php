<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;

/**
 * Class Priority
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Priority extends AbstractModel implements TicketPriorityInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(PriorityResourceModel::class);
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
        \Aheadworks\Helpdesk2\Api\Data\TicketPriorityExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
