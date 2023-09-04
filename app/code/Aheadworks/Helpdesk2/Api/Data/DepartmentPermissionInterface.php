<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface DepartmentPermissionInterface
 * @api
 */
interface DepartmentPermissionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const VIEW_ROLE_IDS = 'view_role_ids';
    const UPDATE_ROLE_IDS = 'update_role_ids';
    /**#@-*/

    const ALL_ROLES_ID = 0;

    /**#@+
     * Permission type constants
     */
    const TYPE_VIEW = 'view';
    const TYPE_UPDATE = 'update';
    /**#@-*/

    /**
     * Get view role IDs
     *
     * @return int[]
     */
    public function getViewRoleIds();

    /**
     * Set view role IDs
     *
     * @param int[] $roleIds
     * @return $this
     */
    public function setViewRoleIds($roleIds);

    /**
     * Get update role IDs
     *
     * @return int[]
     */
    public function getUpdateRoleIds();

    /**
     * Set update role ids
     *
     * @param int[] $roleIds
     * @return $this
     */
    public function setUpdateRoleIds($roleIds);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionExtensionInterface $extensionAttributes
    );
}
