<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Event;

use Aheadworks\Helpdesk2\Api\ValidateAutomationInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Pool as ActionPool;
use Aheadworks\Helpdesk2\Model\Automation\Loader as AutomationLoader;

class EventHandler
{
    /**
     * @var AutomationLoader
     */
    private $automationLoader;

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var ValidateAutomationInterface
     */
    private $validateAutomation;

    /**
     * EventHandler constructor.
     * @param AutomationLoader $automationLoader
     * @param ActionPool $actionPool
     * @param ValidateAutomationInterface $validateAutomation
     */
    public function __construct(
        AutomationLoader $automationLoader,
        ActionPool $actionPool,
        ValidateAutomationInterface $validateAutomation
    ) {
        $this->automationLoader = $automationLoader;
        $this->actionPool = $actionPool;
        $this->validateAutomation = $validateAutomation;
    }

    /**
     * Trigger automation event handler
     *
     * @param EventDataInterface $eventData
     * @return $this
     * @throws LocalizedException
     */
    public function trigger(EventDataInterface $eventData)
    {
        $automationList = $this->automationLoader->loadByEventName($eventData->getEventName());
        foreach ($automationList as $automation) {
            if (!$this->validateAutomation->isValid($automation, $eventData)) {
                continue;
            }

            /** @var array $actions */
            $actions = $automation->getActions();
            foreach ($actions as $actionData) {
                if (!isset($actionData['action']) || !isset($actionData['value'])) {
                    throw new LocalizedException(__('Automation action data is not correct'));
                }

                $actionHandler = $this->actionPool->getActionHandler($actionData['action']);
                $actionHandler->run($actionData, $eventData);
            }
            if ($automation->getIsRequiredToBreak()) {
                break;
            }
        }

        return $this;
    }
}