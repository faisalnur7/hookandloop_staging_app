<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Processor;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Processor
 */
interface ProcessorInterface
{
    /**
     * Process gateway
     *
     * @param GatewayDataInterface $gateway
     */
    public function process($gateway);
}
