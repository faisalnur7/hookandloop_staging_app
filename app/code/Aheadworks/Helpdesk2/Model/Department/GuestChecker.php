<?php
namespace Aheadworks\Helpdesk2\Model\Department;

use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GuestChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class GuestChecker
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $repository;

    /**
     * @param UserContextInterface $userContext
     * @param DepartmentRepositoryInterface $repository
     */
    public function __construct(
        UserContextInterface $userContext,
        DepartmentRepositoryInterface $repository
    ) {
        $this->userContext = $userContext;
        $this->repository = $repository;
    }

    /**
     * Check if the department can be used by a guest
     *
     * @param int $departmentId
     * @return bool
     * @throws LocalizedException
     */
    public function canBeUsedByGuest($departmentId)
    {
        $department = $this->repository->get($departmentId);
        $isGuest = $this->userContext->getUserType() != $this->userContext::USER_TYPE_CUSTOMER;

        return !($isGuest && !$department->getIsAllowGuest());
    }
}
