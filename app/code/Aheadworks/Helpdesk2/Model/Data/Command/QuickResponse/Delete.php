<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse
 */
class Delete implements CommandInterface
{
    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     */
    public function __construct(
        QuickResponseRepositoryInterface $quickResponseRepository
    ) {
        $this->quickResponseRepository = $quickResponseRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[QuickResponseInterface::ID])) {
            throw new \InvalidArgumentException(
                'ID param is required to delete response'
            );
        }

        $this->quickResponseRepository->deleteById($data[QuickResponseInterface::ID]);
        return true;
    }
}
