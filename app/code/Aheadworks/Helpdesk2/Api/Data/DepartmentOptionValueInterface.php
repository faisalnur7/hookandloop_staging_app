<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface DepartmentOptionValueInterface
 * @api
 */
interface DepartmentOptionValueInterface extends ExtensibleDataInterface, StorefrontLabelEntityInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const OPTION_ID = 'option_id';
    const SORT_ORDER = 'sort_order';
    /**#@-*/

    const STOREFRONT_LABEL_ENTITY_TYPE = 'department_option_value';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get option ID
     *
     * @return int
     */
    public function getOptionId();

    /**
     * Set option ID
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueExtensionInterface $extensionAttributes
    );
}
