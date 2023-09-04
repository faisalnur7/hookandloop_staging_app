<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface as PatternInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern
 */
class ChangeStatus implements CommandInterface
{
    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param RejectingPatternRepositoryInterface $patternRepository
     */
    public function __construct(
        RejectingPatternRepositoryInterface $patternRepository
    ) {
        $this->patternRepository = $patternRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[PatternInterface::IS_ACTIVE]) || (!isset($data[PatternInterface::ID]))) {
            throw new \InvalidArgumentException(
                'Status and ID params are required to change status'
            );
        }

        $isActive = (bool)$data[PatternInterface::IS_ACTIVE];
        $pattern = $this->patternRepository->get($data[PatternInterface::ID]);

        if ($pattern->getIsActive() == $isActive) {
            return false;
        }

        $pattern->setIsActive($isActive);
        return $this->patternRepository->save($pattern);
    }
}
