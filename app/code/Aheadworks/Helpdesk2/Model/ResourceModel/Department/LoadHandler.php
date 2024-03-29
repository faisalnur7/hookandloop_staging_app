<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department;

use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Repository as OptionRepository;

/**
 * Class LoadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department
 */
class LoadHandler
{
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param OptionRepository $optionRepository
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        OptionRepository $optionRepository,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->optionRepository = $optionRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Load additional data for model
     *
     * @param Department $model
     * @param int $storeId
     * @throws \Exception
     */
    public function load($model, $storeId)
    {
        $this->loadDepartmentOptions($model, $storeId);
    }

    /**
     * Load department options
     *
     * @param Department $model
     * @param int $storeId
     * @throws \Exception
     */
    private function loadDepartmentOptions($model, $storeId)
    {
        $optionObjects = $this->optionRepository->getByDepartmentId($model->getId(), $storeId);
        $optionData = [];
        foreach ($optionObjects as $optionObject) {
            $optionData[] = $this->dataObjectProcessor->buildOutputDataArray(
                $optionObject,
                DepartmentOptionInterface::class
            );
        }

        $model->setOptions($optionData);
    }
}
