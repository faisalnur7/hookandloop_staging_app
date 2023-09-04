<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Gateway connection provider
 */
interface ConnectionInterface
{
    /**
     * Get connection
     *
     * @param GatewayDataInterface $gateway
     * @return object
     */
    public function getConnection(GatewayDataInterface $gateway): object;
}
