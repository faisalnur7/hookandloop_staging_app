<?php
namespace Aheadworks\Helpdesk2\Model\Department;

use Magento\Store\Api\StoreRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class ConfigChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class ConfigChecker
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $primaryDepartments;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param Config $config
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        Config $config
    ) {
        $this->storeRepository = $storeRepository;
        $this->config = $config;
    }

    /**
     * Check if department set as primary in config
     *
     * @param int $departmentId
     * @return bool
     */
    public function isSetAsPrimary($departmentId)
    {
        if ($this->primaryDepartments === null) {
            $this->primaryDepartments = $this->getPrimaryDepartments();
        }

        return in_array($departmentId, $this->primaryDepartments);
    }

    /**
     * Get primary departments
     *
     * @return array
     */
    private function getPrimaryDepartments()
    {
        $departments = [];
        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $departments[] = $this->config->getPrimaryDepartment($store->getId());
        }

        return array_unique($departments);
    }
}
