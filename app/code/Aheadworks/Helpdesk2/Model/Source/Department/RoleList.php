<?php
namespace Aheadworks\Helpdesk2\Model\Source\Department;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Authorization\Model\Role as RoleModel;
use Magento\Authorization\Model\ResourceModel\Role\Collection as RoleCollection;
use Magento\Authorization\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;

/**
 * Class RoleList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Department
 */
class RoleList implements OptionSourceInterface
{
    /**
     * @var RoleCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param RoleCollectionFactory $collectionFactory
     */
    public function __construct(
        RoleCollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = [];

            /** @var RoleCollection $collection */
            $collection = $this->collectionFactory->create();
            $collection->setRolesFilter();
            $options[] = [
                'value' => DepartmentPermissionInterface::ALL_ROLES_ID,
                'label' => __('All roles'),
            ];

            /** @var RoleModel $role */
            foreach ($collection as $role) {
                $options[] = [
                    'value' => $role->getData('role_id'),
                    'label' => $role->getData('role_name'),
                ];
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
