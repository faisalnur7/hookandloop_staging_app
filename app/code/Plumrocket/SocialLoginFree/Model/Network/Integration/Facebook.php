<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Lib\Http\Client\Curl;
use Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel;
use Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;

/**
 * @since 4.0.4
 */
class Facebook implements NetworkConnectorInterface
{
    private const CODE = "facebook";
    private const BASE_AUTH_URL = "https://graph.facebook.com/oauth/access_token";
    private const ME_URL = "https://graph.facebook.com/me";

    /**
     * @var string[]
     */
    private $_fields = [
        'user_id' => 'id',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'email' => 'email',
        'dob' => 'birthday',
        'gender' => 'gender',
        'photo' => 'picture',
    ];

    /**
     * @var \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl
     */
    private $client;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface
     */
    private $networkLogger;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel
     */
    private $networkAccountModel;

    /**
     * @param \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl                       $client
     * @param \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface $networkLogger
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                      $networkConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver     $modalCallbackUrlResolver
     * @param \Magento\Framework\Serialize\SerializerInterface                       $serializer
     * @param \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel    $networkAccountModel
     */
    public function __construct(
        Curl $client,
        NetworkLoggerInterface $networkLogger,
        Network $networkConfig,
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        SerializerInterface $serializer,
        CreateNetworkAccountModel $networkAccountModel
    ) {
        $this->client = $client;
        $this->networkLogger = $networkLogger;
        $this->networkConfig = $networkConfig;
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->serializer = $serializer;
        $this->networkAccountModel = $networkAccountModel;
    }

    /**
     * @inheritDoc
     */
    public function getNetworkAccount(array $networkResponse): NetworkAccountInterface
    {
        $userInfo = $this->getData(
            $this->getAccessToken($networkResponse['code'])
        );

        return $this->networkAccountModel->execute(
            self::CODE,
            $userInfo,
            $this->_fields
        );
    }

    /**
     * Get access token.
     *
     * @param string $code
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAccessToken(string $code): string
    {
        $this->networkLogger->add(self::CODE, "Get login token");

        $this->client->reset();
        $this->client->get(
            self::BASE_AUTH_URL,
            [
                "client_id" => $this->networkConfig->getApplicationId(self::CODE),
                "client_secret" => $this->networkConfig->getApplicationSecretKey(self::CODE),
                "code" => $code,
                "redirect_uri" => $this->modalCallbackUrlResolver->getUrl(self::CODE),
            ]
        );

        $body = $this->client->getBody();

        if (! $body) {
            throw new LocalizedException(__("Cannot get token."));
        }

        $response = $this->serializer->unserialize($body);

        if (empty($response['access_token'])) {
            throw new LocalizedException(__("Cannot get token."));
        }

        return $response['access_token'];
    }

    /**
     * Get user data.
     *
     * @param string $token
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getData(string $token): array
    {
        $this->client->reset();
        $this->client->post(
            self::ME_URL,
            [
                "access_token" => $token,
                "fields" => implode(',', $this->_fields),
            ]
        );

        $body = $this->client->getBody();

        if (! $body) {
            throw new LocalizedException(__('Cannot get token.'));
        }

        $response = $this->serializer->unserialize($body);
        $response["picture"] = $response["picture"]["data"]["url"];

        return $response;
    }
}
