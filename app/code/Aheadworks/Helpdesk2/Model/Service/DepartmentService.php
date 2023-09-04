<?php
namespace Aheadworks\Helpdesk2\Model\Service;

use Aheadworks\Helpdesk2\Api\DepartmentManagementInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Gateway\ProcessorPool;

/**
 * Class DepartmentService
 *
 * @package Aheadworks\Helpdesk2\Model\Service
 */
class DepartmentService implements DepartmentManagementInterface
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var ProcessorPool
     */
    private $processorPool;

    /**
     * @param SearchBuilder $searchBuilder
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        SearchBuilder $searchBuilder,
        GatewayRepositoryInterface $gatewayRepository,
        ProcessorPool $processorPool
    ) {
        $this->searchBuilder = $searchBuilder;
        $this->gatewayRepository = $gatewayRepository;
        $this->processorPool = $processorPool;
    }

    /**
     * @inheritdoc
     */
    public function processGateways()
    {
        $departmentList = $this->searchBuilder->addIsActiveFilter()->searchDepartments();
        foreach ($departmentList as $department) {
            $gatewayIds = $department->getGatewayIds();
            foreach ($gatewayIds as $gatewayId) {
                $gateway = $this->gatewayRepository->get($gatewayId);
                if (!$gateway->getIsActive()) {
                    continue;
                }
                $processor = $this->processorPool->getProcessor($gateway->getType());
                $processor->process($gateway);
            }
        }

        return true;
    }
}
