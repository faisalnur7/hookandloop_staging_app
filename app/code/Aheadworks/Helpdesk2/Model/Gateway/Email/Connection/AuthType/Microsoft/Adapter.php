<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Microsoft;

use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\AdapterInterface;
use League\OAuth2\Client\Provider\GenericProvider as OAuth2Client;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Microsoft oauth adapter
 */
class Adapter implements AdapterInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var OAuth2Client
     */
    private $client;

    /**
     * @param Config $config
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * Create access token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return AccessToken
     * @throws LocalizedException
     */
    public function createAccessToken(GatewayDataInterface $gateway, ?string $code = null): AccessToken
    {
        if (!$gateway->getClientId() || !$gateway->getClientSecret()) {
            throw new \InvalidArgumentException('Authorization keys are not provided');
        }

        try {
            $oauthClient = $this->getOAthClient($gateway);
            if ($code) {
                return $oauthClient->getAccessToken('authorization_code', [
                    'code' => $code
                ]);
            }

            if (!$gateway->getAccessToken()) {
                throw new \InvalidArgumentException('Please verify your Microsoft account');
            }

            $accessToken = $this->getToken($gateway->getAccessToken());
            if ($this->isAccessTokenExpired($accessToken)) {
                return $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $accessToken->getRefreshToken()
                ]);
            }

            return $accessToken;
        } catch (IdentityProviderException $e) {
            throw new LocalizedException(
                __('Error on requesting access token: %1', $e->getMessage()),
            );
        }
    }

    /**
     * Get email by token
     *
     * @param GatewayDataInterface $gateway
     * @param AccessToken $accessToken
     * @return string
     * @throws LocalizedException
     */
    public function getEmailByToken(GatewayDataInterface $gateway, $accessToken): string
    {
        $oauthClient = $this->getOAthClient($gateway);
        $user = $oauthClient->getResourceOwner($accessToken);
        $userData = $user->toArray();

        if (!isset($userData['EmailAddress'])) {
            throw new LocalizedException(
                __('Unable to retrieve email address from Microsoft connector'),
            );
        }

        return $userData['EmailAddress'];
    }

    /**
     * Convert token to array
     *
     * @param object|array $accessToken
     * @return array
     */
    public function convertTokenToArray($accessToken): array
    {
        if (is_array($accessToken)) {
            return $accessToken;
        }

        if ($accessToken instanceof AccessToken) {
            return $accessToken->jsonSerialize();
        }

        throw new \InvalidArgumentException('Access token cannot be converted into array');
    }

    /**
     * Check if access token is expired
     *
     * @param AccessToken|array $accessToken
     * @return bool
     */
    public function isAccessTokenExpired($accessToken): bool
    {
        if (is_array($accessToken)) {
            $accessToken = $this->getToken($accessToken);
        }

        return $accessToken->hasExpired();
    }

    /**
     * Get token
     *
     * @param array $token
     * @return AccessToken
     */
    private function getToken(array $token): AccessToken
    {
        return $this->objectManager->create(AccessToken::class, ['options' => $token]);
    }

    /**
     * Get OAuth client
     *
     * @param GatewayDataInterface $gateway
     * @return OAuth2Client
     */
    private function getOAthClient(GatewayDataInterface $gateway): OAuth2Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $this->client = $this->objectManager->create(
            OAuth2Client::class,
            [
                'options' => [
                    'clientId'                => $gateway->getClientId(),
                    'clientSecret'            => $gateway->getClientSecret(),
                    'redirectUri'             => $this->config->getRedirectUri(),
                    'urlAuthorize'            => $this->config->getAuthorizeUrl($gateway->getTenantId()),
                    'urlAccessToken'          => $this->config->getAccessTokenUrl($gateway->getTenantId()),
                    'urlResourceOwnerDetails' => $this->config->getResourceOwnerDetailsUrl()
                ]
            ]
        );

        return $this->client;
    }
}
