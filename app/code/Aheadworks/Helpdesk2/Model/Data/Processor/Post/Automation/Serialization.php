<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Serialization
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation
 */
class Serialization implements ProcessorInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        JsonSerializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[AutomationInterface::CONDITIONS]) && is_array($data[AutomationInterface::CONDITIONS])) {
            $data[AutomationInterface::CONDITIONS] = $this->jsonSerializer->serialize(
                $data[AutomationInterface::CONDITIONS]
            );
        }

        if (isset($data[AutomationInterface::ACTIONS]) && is_array($data[AutomationInterface::ACTIONS])) {
            $data[AutomationInterface::ACTIONS] = $this->jsonSerializer->serialize(
                $data[AutomationInterface::ACTIONS]
            );
        }

        return $data;
    }
}
