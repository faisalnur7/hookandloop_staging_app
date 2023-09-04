<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department;

use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Department\Permission\Converter as PermissionConverter;

/**
 * Class Permission
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department
 */
class Permission implements ProcessorInterface
{
    /**
     * @var PermissionConverter
     */
    private $permissionConverter;

    /**
     * @param PermissionConverter $permissionConverter
     */
    public function __construct(
        PermissionConverter $permissionConverter
    ) {
        $this->permissionConverter = $permissionConverter;
    }

    /**
     * Prepare model before save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelBeforeSave($department)
    {
        return $department;
    }

    /**
     * Prepare gateway permissions
     *
     * Permission object is received when using entity manager
     * Permission array is received when loading collection
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelAfterLoad($department)
    {
        $permissions = $department->getPermissions();
        if ($permissions && is_array($permissions)) {
            $department->setPermissions($this->permissionConverter->fromDbArrayToDataArray($permissions));
        }

        return $department;
    }
}
