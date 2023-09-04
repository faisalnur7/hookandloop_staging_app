<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\ConnectionInterface;

class ConnectionProvider
{
    /**
     * @var ConnectionInterface[]
     */
    private $authModelList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConnectionInterface[] $authModelList
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        array $authModelList
    ) {
        $this->authModelList = $authModelList;
    }

    /**
     * Get gateway connection
     *
     * @param GatewayDataInterface $gateway
     * @return object
     */
    public function getConnection($gateway)
    {
        if (!array_key_exists($gateway->getAuthorizationType(), $this->authModelList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway authorization type: %s', $gateway->getAuthorizationType())
            );
        }

        /** @var ConnectionInterface $connectionModel */
        $connectionModel = $this->objectManager->create(
            $this->authModelList[$gateway->getAuthorizationType()]
        );

        return $connectionModel->getConnection($gateway);
    }
}
