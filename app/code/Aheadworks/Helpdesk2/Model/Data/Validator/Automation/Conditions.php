<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Automation;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Conditions
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Automation
 */
class Conditions extends AbstractValidator
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
     * Check duplications of conditions
     *
     * @param AutomationInterface $automation
     * @return bool
     * @throws \Exception
     */
    public function isValid($automation)
    {
        $this->_clearMessages();

        $conditions = $this->jsonSerializer->unserialize($automation->getConditions());

        $hashes = [];
        foreach ($conditions as $conditionData) {
            unset($conditionData['record_id']);
            $hash = $this->createHash($conditionData);
            if (in_array($hash, $hashes)) {
                $this->_addMessages(
                    [
                        __('Duplication of Conditions is not possible.')
                    ]
                );
                break;
            }
            $hashes[] = $hash;
        }

        return empty($this->getMessages());
    }

    /**
     * Create array hash
     *
     * @param array $data
     * @return string
     */
    private function createHash($data)
    {
        ksort($data);
        $hashArray = array_reduce($data, function ($carry, $item) {
            if (is_array($item)) {
                $carry[] = $this->createHash($item);
            } else {
                $carry[] = $item;
            }

            return $carry;
        });

        return implode('|', $hashArray);
    }
}
