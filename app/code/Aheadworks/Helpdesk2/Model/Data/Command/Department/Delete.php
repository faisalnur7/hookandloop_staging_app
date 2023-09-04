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
class Delete implements CommandInterface
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
        if (!isset($data[DepartmentInterface::ID])) {
            throw new \InvalidArgumentException(
                'Department ID param is required to delete'
            );
        }

        $department = $this->departmentRepository->get($data[DepartmentInterface::ID]);
        return $this->departmentRepository->delete($department);
    }
}
