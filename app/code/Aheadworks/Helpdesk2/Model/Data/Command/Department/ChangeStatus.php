<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Department;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Department
 */
class ChangeStatus implements CommandInterface
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[DepartmentInterface::IS_ACTIVE]) || (!isset($data[DepartmentInterface::ID]))) {
            throw new \InvalidArgumentException(
                'Status and ID params are required to change status'
            );
        }

        $isActive = (bool)$data[DepartmentInterface::IS_ACTIVE];
        $department = $this->departmentRepository->get($data[DepartmentInterface::ID]);

        if ($department->getIsActive() == $isActive) {
            return false;
        }

        $department->setIsActive($isActive);
        return $this->departmentRepository->save($department);
    }
}
