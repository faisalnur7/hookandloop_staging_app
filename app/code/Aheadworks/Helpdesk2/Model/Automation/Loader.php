<?php
namespace Aheadworks\Helpdesk2\Model\Automation;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Model\Automation\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;

/**
 * Class Loader
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
class Loader
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param SearchBuilder $searchBuilder
     * @param JsonSerializer $jsonSerializer
     * @param AutomationRepositoryInterface $automationRepository
     */
    public function __construct(
        SearchBuilder $searchBuilder,
        JsonSerializer $jsonSerializer,
        AutomationRepositoryInterface $automationRepository
    ) {
        $this->searchBuilder = $searchBuilder;
        $this->jsonSerializer = $jsonSerializer;
        $this->automationRepository = $automationRepository;
    }

    /**
     * Get list with automations for event
     *
     * @param string $eventName
     * @return AutomationInterface[]
     * @throws LocalizedException
     */
    public function loadByEventName($eventName)
    {
        $automationList = $this->searchBuilder
            ->addIsActiveFilter()
            ->addEventFilter($eventName)
            ->addPrioritySorting()
            ->searchAutomations();

        foreach ($automationList as $automation) {
            $this->unserializeData($automation);
        }

        return $automationList;
    }

    /**
     * Load automation by ID
     *
     * @param int $automationId
     * @return AutomationInterface
     * @throws NoSuchEntityException
     */
    public function loadById($automationId)
    {
        if (!isset($this->registry[$automationId])) {
            $automation = $this->automationRepository->get($automationId);
            $this->unserializeData($automation);
            $this->registry[$automationId] = $automation;
        }

        return $this->registry[$automationId];
    }

    /**
     * Unserialize automation data
     *
     * @param AutomationInterface $automation
     * @return AutomationInterface
     */
    private function unserializeData($automation)
    {
        $automation
            ->setConditions($this->jsonSerializer->unserialize($automation->getConditions()))
            ->setActions($this->jsonSerializer->unserialize($automation->getActions()));

        return $automation;
    }
}
