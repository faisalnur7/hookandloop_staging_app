<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway\SecretData as SecretDataFormProcessor;

/**
 * Class SecretData
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Gateway
 */
class SecretData implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if ($data[GatewayDataInterface::CLIENT_SECRET]
            && $data[GatewayDataInterface::CLIENT_SECRET] == SecretDataFormProcessor::PASSWORD_MASK
        ) {
            unset($data[GatewayDataInterface::CLIENT_SECRET]);
        }

        return $data;
    }
}
