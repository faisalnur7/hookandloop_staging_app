<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Department;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Gateway
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var DepartmentInterfaceFactory
     */
    private $departmentFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param DepartmentInterfaceFactory $departmentFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DepartmentRepositoryInterface $departmentRepository,
        DepartmentInterfaceFactory $departmentFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->departmentRepository = $departmentRepository;
        $this->departmentFactory = $departmentFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($departmentData)
    {
        $department = $this->getDepartmentObject($departmentData);
        $this->dataObjectHelper->populateWithArray(
            $department,
            $departmentData,
            DepartmentInterface::class
        );

        return $this->departmentRepository->save($department);
    }

    /**
     * Get department object
     *
     * @param array $departmentData
     * @return DepartmentInterface
     * @throws NoSuchEntityException
     */
    private function getDepartmentObject($departmentData)
    {
        return isset($departmentData[DepartmentInterface::ID])
            ? $this->departmentRepository->get($departmentData[DepartmentInterface::ID])
            : $this->departmentFactory->create();
    }
}
