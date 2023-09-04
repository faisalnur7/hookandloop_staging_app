<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @var RejectingPatternInterfaceFactory
     */
    private $patternFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param RejectingPatternRepositoryInterface $patternRepository
     * @param RejectingPatternInterfaceFactory $patternFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        RejectingPatternRepositoryInterface $patternRepository,
        RejectingPatternInterfaceFactory $patternFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->patternRepository = $patternRepository;
        $this->patternFactory = $patternFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($patternData)
    {
        $pattern = $this->getPatternObject($patternData);
        $this->dataObjectHelper->populateWithArray(
            $pattern,
            $patternData,
            RejectingPatternInterface::class
        );

        return $this->patternRepository->save($pattern);
    }

    /**
     * Get pattern object
     *
     * @param array $patternData
     * @return RejectingPatternInterface
     * @throws NoSuchEntityException
     */
    private function getPatternObject($patternData)
    {
        return isset($gatewayData[RejectingPatternInterface::ID])
            ? $this->patternRepository->get($patternData[RejectingPatternInterface::ID])
            : $this->patternFactory->create();
    }
}
