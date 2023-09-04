<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class SecretData
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway
 */
class SecretData implements ProcessorInterface
{
    const PASSWORD_MASK = '*****';

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if ($data[GatewayDataInterface::CLIENT_SECRET]) {
            $data[GatewayDataInterface::CLIENT_SECRET] = self::PASSWORD_MASK;
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
