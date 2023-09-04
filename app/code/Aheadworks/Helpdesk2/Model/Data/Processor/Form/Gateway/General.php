<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class General
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway
 */
class General implements ProcessorInterface
{
    const IS_GATEWAY_SAVED = 'is_gateway_saved';

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[GatewayDataInterface::ID])) {
            $data[self::IS_GATEWAY_SAVED] = true;
        }
        if (isset($data[GatewayDataInterface::IS_VERIFIED])) {
            $data[GatewayDataInterface::IS_VERIFIED] = (bool)($data[GatewayDataInterface::IS_VERIFIED]);
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
