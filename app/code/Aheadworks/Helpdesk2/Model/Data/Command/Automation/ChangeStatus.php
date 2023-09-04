<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Automation;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Automation
 */
class ChangeStatus implements CommandInterface
{
    /**
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @param AutomationRepositoryInterface $automationRepository
     */
    public function __construct(
        AutomationRepositoryInterface $automationRepository
    ) {
        $this->automationRepository = $automationRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[AutomationInterface::IS_ACTIVE]) || (!isset($data[AutomationInterface::ID]))) {
            throw new \InvalidArgumentException(
                'Status and ID params are required to change status'
            );
        }

        $isActive = (bool)$data[AutomationInterface::IS_ACTIVE];
        $automation = $this->automationRepository->get($data[AutomationInterface::ID]);

        if ($automation->getIsActive() == $isActive) {
            return false;
        }

        $automation->setIsActive($isActive);
        return $this->automationRepository->save($automation);
    }
}
