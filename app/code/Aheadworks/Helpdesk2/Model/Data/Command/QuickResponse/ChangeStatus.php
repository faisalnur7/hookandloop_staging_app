<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse
 */
class ChangeStatus implements CommandInterface
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
        if (!isset($data[QuickResponseInterface::IS_ACTIVE]) || (!isset($data[QuickResponseInterface::ID]))) {
            throw new \InvalidArgumentException(
                'Status and ID params are required to change status'
            );
        }

        $isActive = (bool)$data[QuickResponseInterface::IS_ACTIVE];
        $quickResponse = $this->quickResponseRepository->get($data[QuickResponseInterface::ID]);

        if ($quickResponse->getIsActive() == $isActive) {
            return false;
        }

        $quickResponse->setIsActive($isActive);
        return $this->quickResponseRepository->save($quickResponse);
    }
}
