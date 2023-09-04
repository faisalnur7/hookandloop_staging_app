<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Laminas\Mail\Storage\AbstractStorage;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\ProtocolFactory;
use Aheadworks\Helpdesk2\Model\Gateway\Email\StorageFactory;
use Aheadworks\Helpdesk2\Model\Gateway\ParamExtractor;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol\AdapterInterface;

/**
 * OAuth connector
 */
class OAuthConnector
{
    const ACCESS_TOKEN_INDEX = 'access_token';

    /**
     * @var ProtocolFactory
     */
    private ProtocolFactory $protocolFactory;

    /**
     * @var StorageFactory
     */
    private StorageFactory $storageFactory;

    /**
     * @var ParamExtractor
     */
    private ParamExtractor $paramExtractor;

    /**
     * @param ProtocolFactory $protocolFactory
     * @param ParamExtractor $paramExtractor
     * @param StorageFactory $storageFactory
     */
    public function __construct(
        ProtocolFactory $protocolFactory,
        ParamExtractor $paramExtractor,
        StorageFactory $storageFactory
    ) {
        $this->protocolFactory = $protocolFactory;
        $this->paramExtractor = $paramExtractor;
        $this->storageFactory = $storageFactory;
    }

    /**
     * Get connection to gateway
     *
     * @param GatewayDataInterface $gateway
     * @return AbstractStorage|null
     */
    public function getConnection(GatewayDataInterface $gateway): ?AbstractStorage
    {
        if ($gateway->getIsVerified()) {
            $protocolAdapter = $this->getProtocolAdapter($gateway);
            if ($this->isConnectionValid($protocolAdapter, $gateway)) {
                return $this->storageFactory->createByProtocolObject(
                    $gateway->getGatewayProtocol(),
                    $protocolAdapter->getProtocol()
                );
            }
        }

        return null;
    }

    /**
     * Is gateway valid
     *
     * @param GatewayDataInterface $gateway
     * @return bool
     */
    public function isGatewayValid(GatewayDataInterface $gateway): bool
    {
        $protocolAdapter = $this->getProtocolAdapter($gateway);
        return $this->isConnectionValid($protocolAdapter, $gateway);
    }

    /**
     * Is connection valid
     *
     * @param AdapterInterface $protocolAdapter
     * @param GatewayDataInterface $gateway
     * @return bool
     */
    private function isConnectionValid(AdapterInterface $protocolAdapter, GatewayDataInterface $gateway): bool
    {
        $accessToken = $gateway->getAccessToken();
        $xoauthString = $this->constructAuthString(
            $gateway->getEmail(),
            $accessToken[self::ACCESS_TOKEN_INDEX]
        );

        return $protocolAdapter->sendRequest($xoauthString);
    }

    /**
     * Get protocol adapter
     *
     * @param GatewayDataInterface $gateway
     * @return AdapterInterface
     */
    private function getProtocolAdapter(GatewayDataInterface $gateway): AdapterInterface
    {
        $params = $this->paramExtractor->extract($gateway);
        return $this->protocolFactory->create($params);
    }

    /**
     * Build an OAuth2 authentication string for the given email address and access token
     *
     * @param string $user
     * @param string $accessToken
     * @return string
     */
    private function constructAuthString(string $user, string $accessToken): string
    {
        return base64_encode("user=$user\1auth=Bearer $accessToken\1\1");
    }
}
