<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;

/**
 * Class Option
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Option extends AbstractExtensibleObject implements TicketOptionInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
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
    public function getTicketId()
    {
        return $this->_get(self::TICKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
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
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * Get is encrypted
     *
     * @return bool
     */
    public function getIsEncrypted(): bool
    {
        return (bool)$this->_get(self::IS_ENCRYPTED);
    }

    /**
     * Set is encrypted
     *
     * @param bool $isEncrypted
     * @return $this
     */
    public function setIsEncrypted(bool $isEncrypted): self
    {
        return $this->setData(self::IS_ENCRYPTED, $isEncrypted);
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
        \Aheadworks\Helpdesk2\Api\Data\TicketOptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
