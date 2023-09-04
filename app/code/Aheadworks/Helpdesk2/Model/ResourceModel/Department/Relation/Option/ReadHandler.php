<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Option;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Repository;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Option
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Repository
     */
    private $departmentOptionRepository;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Repository $departmentOptionRepository
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        Repository $departmentOptionRepository
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->departmentOptionRepository = $departmentOptionRepository;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getId()) {
            return $entity;
        }

        $options = $this->departmentOptionRepository->getByDepartmentId($entity->getId(), $arguments['store_id']);
        $entity->setOptions($options);

        return $entity;
    }
}
