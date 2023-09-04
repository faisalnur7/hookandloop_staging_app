<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Lib\Http\Client\Curl;
use Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\TwitterComposite;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;
use Plumrocket\SocialLoginFree\Exception\UserAccessDeniedException;

/**
 * @since 4.0.0
 */
class Connector implements NetworkConnectorInterface
{
    private const ACCOUNT_DATA_URL_V2 = 'https://api.twitter.com/2/users/me';
    private const ACCESS_TOKEN_URL_V2 = 'https://api.twitter.com/2/oauth2/token';

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    private $urlHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl
     */
    private $client;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel
     */
    private $networkAccountModel;

    /**
     * @param \Magento\Framework\Url\Helper\Data                                  $urlHelper
     * @param \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl                    $client
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                   $networkConfig
     * @param \Magento\Framework\Serialize\SerializerInterface                    $serializer
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver  $modalCallbackUrlResolver
     * @param \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel $networkAccountModel
     */
    public function __construct(
        UrlHelper $urlHelper,
        Curl $client,
        Network $networkConfig,
        SerializerInterface $serializer,
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        CreateNetworkAccountModel $networkAccountModel
    ) {
        $this->urlHelper = $urlHelper;
        $this->client = $client;
        $this->networkConfig = $networkConfig;
        $this->serializer = $serializer;
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->networkAccountModel = $networkAccountModel;
    }

    /**
     * @inheritDoc
     */
    public function getNetworkAccount(array $networkResponse): NetworkAccountInterface
    {
        if (! empty($networkResponse["error"])) {
            throw new UserAccessDeniedException(__("Access denied was canceled by user"));
        }

        $token = $this->getAccessTokenV2($networkResponse['code']);
        $this->client->reset();
        $this->client->addHeader('Authorization', 'Bearer ' . $token);
        $url = $this->urlHelper->addRequestParam(
            self::ACCOUNT_DATA_URL_V2,
            [
                'user.fields' => 'id,name,profile_image_url,verified,entities,protected,public_metrics',
            ]
        );
        $this->client->get($url);

        $data = $this->serializer->unserialize($this->client->getBody())['data'] ?? [];
        if (! $data) {
            throw new LocalizedException(__('Cannot get token.'));
        }

        [$firstName, $lastName] = explode(' ', $data['name']);
        $data['firstName'] = $firstName;
        $data['lastName'] = $lastName;

        return $this->networkAccountModel->execute(
            TwitterComposite::CODE,
            $data,
            [
                'user_id' => 'id',
                'firstname' => 'firstName',
                'lastname' => 'lastName',
                'photo' => 'profile_image_url',
            ]
        );
    }

    /**
     * Get access token for OAuth v2
     *
     * @param string $code
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAccessTokenV2(string $code): string
    {
        $this->client->reset();
        $this->client->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $clientId = $this->networkConfig->getNetworkConfig(TwitterComposite::CODE, 'client_id');
        $clientSecret = $this->networkConfig->getNetworkConfig(TwitterComposite::CODE, 'client_secret');
        $this->client->addHeader('Authorization', 'Basic ' . base64_encode("$clientId:$clientSecret"));
        $this->client->post(
            self::ACCESS_TOKEN_URL_V2,
            [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'redirect_uri' => $this->modalCallbackUrlResolver->getUrl(TwitterComposite::CODE),
                'code_verifier' => TwitterComposite::CODE,
            ]
        );

        if (! $body = $this->client->getBody()) {
            throw new LocalizedException(__('Cannot get token.'));
        }
        return $this->serializer->unserialize($body)['access_token'] ?? '';
    }
}
