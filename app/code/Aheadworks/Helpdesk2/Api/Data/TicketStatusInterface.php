<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TicketStatusInterface
 * @api
 */
interface TicketStatusInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const IS_SYSTEM = 'is_system';
    const LABEL = 'label';

    /**
     * Get ticket status ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ticket status ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get is system
     *
     * @return int
     */
    public function getIsSystem();

    /**
     * Set is system
     *
     * @param int $isSystem
     * @return $this
     */
    public function setIsSystem($isSystem);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketStatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketStatusExtensionInterface $extensionAttributes
    );
}
