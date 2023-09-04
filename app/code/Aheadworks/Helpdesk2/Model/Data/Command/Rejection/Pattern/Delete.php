<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface as PatternInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern
 */
class Delete implements CommandInterface
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
        if (!isset($data[PatternInterface::ID])) {
            throw new \InvalidArgumentException(
                'Pattern ID param is required to remove pattern'
            );
        }

        $this->patternRepository->deleteById($data[PatternInterface::ID]);
        return true;
    }
}
