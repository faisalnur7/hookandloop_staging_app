<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

/**
 * OAuth model connection
 */
abstract class OAuthModel implements ConnectionInterface
{
    /**
     * @var OAuthConnector
     */
    protected OAuthConnector $gatewayConnector;

    /**
     * @var GatewayRepositoryInterface
     */
    protected GatewayRepositoryInterface $gatewayRepository;

    /**
     * @param OAuthConnector $gatewayConnector
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        OAuthConnector $gatewayConnector,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        $this->gatewayConnector = $gatewayConnector;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * Get connection
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function getConnection(GatewayDataInterface $gateway): object
    {
        $gateway = $this->actualizeAuthToken($gateway);
        $gateway = $this->gatewayRepository->get($gateway->getId(), true);
        $connection = $this->gatewayConnector->getConnection($gateway);
        if (!$connection) {
            throw new LocalizedException(
                __('Connection cannot be established for gateway: %1', $gateway->getName()),
            );
        }

        return $connection;
    }

    /**
     * Actualize google auth token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return GatewayDataInterface
     * @throws CouldNotSaveException
     */
    public function actualizeAuthToken(GatewayDataInterface $gateway, ?string $code = null): GatewayDataInterface
    {
        $adapter = $this->getAdapter();
        $token = $gateway->getAccessToken();
        $needToUpdateToken = false;
        if ($token) {
            if ($adapter->isAccessTokenExpired($token)) {
                $needToUpdateToken = true;
            }
        } else {
            if (!$code) {
                throw new \InvalidArgumentException('Please verify your google account');
            }
            $needToUpdateToken = true;
        }

        if ($needToUpdateToken) {
            $token = $adapter->createAccessToken($gateway, $code);
            $gateway->setAccessToken($adapter->convertTokenToArray($token));
            $gateway->setEmail($adapter->getEmailByToken($gateway, $token));
            $isVerified = $this->gatewayConnector->isGatewayValid($gateway);
            $gateway->setIsVerified($isVerified);
            $this->gatewayRepository->save($gateway);
        }

        return $gateway;
    }

    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    abstract protected function getAdapter(): AdapterInterface;
}
