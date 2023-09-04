<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google;

use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Google oauth adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * @var \Google_Client
     */
    private $googleClient;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->googleClient = $objectManager->create(\Google_Client::class);
        $this->config = $config;
    }

    /**
     * Create access token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return array
     */
    public function createAccessToken(GatewayDataInterface $gateway, ?string $code = null): array
    {
        if (!$gateway->getClientId() || !$gateway->getClientSecret()) {
            throw new \InvalidArgumentException('Authorization keys are not provided');
        }

        $this->googleClient->setClientId($gateway->getClientId());
        $this->googleClient->setClientSecret($gateway->getClientSecret());
        $this->googleClient->setRedirectUri($this->config->getRedirectUri());
        $this->googleClient->setAccessType($this->config->getAccessType());

        if ($code) {
            $newAccessToken = $this->googleClient->fetchAccessTokenWithAuthCode($code);
            $this->googleClient->setAccessToken($newAccessToken);
        } else {
            if (!$gateway->getAccessToken()) {
                throw new \InvalidArgumentException('Please verify your google account');
            }
            $this->googleClient->setAccessToken($gateway->getAccessToken());
            if ($this->googleClient->isAccessTokenExpired()) {
                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
            }
        }

        return $this->googleClient->getAccessToken();
    }

    /**
     * Get email by token
     *
     * @param GatewayDataInterface $gateway
     * @param array $accessToken
     * @return string
     */
    public function getEmailByToken(GatewayDataInterface $gateway, $accessToken): string
    {
        $this->googleClient->setAccessToken($accessToken);
        $idToken = $this->googleClient->verifyIdToken();

        return $idToken ? $idToken['email'] : '';
    }

    /**
     * Convert token to array
     *
     * @param array $accessToken
     * @return array
     */
    public function convertTokenToArray($accessToken): array
    {
        if (is_array($accessToken)) {
            return $accessToken;
        }

        throw new \InvalidArgumentException('Access token cannot be converted into array');
    }

    /**
     * Check if access token is expired
     *
     * @param array $accessToken
     * @return bool
     */
    public function isAccessTokenExpired($accessToken): bool
    {
        $this->googleClient->setAccessToken($accessToken);
        return $this->googleClient->isAccessTokenExpired();
    }
}
