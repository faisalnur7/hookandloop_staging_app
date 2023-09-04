<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Merge;

use Magento\Framework\DataObject;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class TicketInfo
 */
class TicketInfo extends DataObject implements TicketInfoInterface
{
    /**
     * Get Uid
     *
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->getData(TicketInterface::UID);
    }

    /**
     * Set Uid
     *
     * @param string $id
     * @return TicketInfoInterface
     */
    public function setUid($id): TicketInfoInterface
    {
        return $this->setData(TicketInterface::UID, $id);
    }

    /**
     * Get entity id
     *
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(TicketInterface::ENTITY_ID);
    }

    /**
     * Set entity id
     *
     * @param int $id
     * @return TicketInfoInterface
     */
    public function setEntityId($id): TicketInfoInterface
    {
        return $this->setData(TicketInterface::ENTITY_ID, $id);
    }

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(TicketInterface::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $date
     * @return TicketInfoInterface
     */
    public function setCreatedAt($date): TicketInfoInterface
    {
        return $this->setData(TicketInterface::CREATED_AT, $date);
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->getData(TicketInterface::CUSTOMER_NAME);
    }

    /**
     * Set customer name
     *
     * @param string $name
     * @return TicketInfoInterface
     */
    public function setCustomerName($name): TicketInfoInterface
    {
        return $this->setData(TicketInterface::CUSTOMER_NAME, $name);
    }

    /**
     * Get subject
     *
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->getData(TicketInterface::SUBJECT);
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return TicketInfoInterface
     */
    public function setSubject($subject): TicketInfoInterface
    {
        return $this->setData(TicketInterface::SUBJECT, $subject);
    }

    /**
     * Set last comment
     *
     * @param string $comment
     * @return TicketInfoInterface
     */
    public function setLastComment($comment): TicketInfoInterface
    {
        return $this->setData(self::LAST_COMMENT, $comment);
    }

    /**
     * Get last comment
     *
     * @return string|null
     */
    public function getLastComment(): ?string
    {
        return $this->getData(self::LAST_COMMENT);
    }
}
