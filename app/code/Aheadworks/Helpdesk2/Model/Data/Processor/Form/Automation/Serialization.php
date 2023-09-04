<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Automation;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

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
        if (isset($data[AutomationInterface::CONDITIONS]) && is_string($data[AutomationInterface::CONDITIONS])) {
            $data[AutomationInterface::CONDITIONS] = $this->jsonSerializer->unserialize(
                $data[AutomationInterface::CONDITIONS]
            );
        }

        if (isset($data[AutomationInterface::ACTIONS]) && is_string($data[AutomationInterface::ACTIONS])) {
            $data[AutomationInterface::ACTIONS] = $this->jsonSerializer->unserialize(
                $data[AutomationInterface::ACTIONS]
            );
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
