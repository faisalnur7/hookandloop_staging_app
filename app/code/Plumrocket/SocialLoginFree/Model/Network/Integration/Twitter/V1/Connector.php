<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Exception\UserAccessDeniedException;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Lib\Http\Client\Curl;
use Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\TwitterComposite;

/**
 * @since 4.0.0
 */
class Connector implements NetworkConnectorInterface
{
    private const ACCOUNT_DATA_URL = 'https://api.twitter.com/1.1/account/verify_credentials.json';
    private const ACCESS_TOKEN_URL = 'https://api.twitter.com/oauth/access_token';

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    private $urlHelper;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    private $encryptor;

    /**
     * @var \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl
     */
    private $client;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel
     */
    private $createNetworkAccountModel;

    /**
     * @param \Magento\Framework\Url\Helper\Data                                  $urlHelper
     * @param \Magento\Framework\Encryption\Encryptor                             $encryptor
     * @param \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl                    $client
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                   $networkConfig
     * @param \Magento\Framework\Session\SessionManagerInterface                  $customerSession
     * @param \Magento\Framework\Serialize\SerializerInterface                    $serializer
     * @param \Magento\Framework\App\RequestInterface                             $request
     * @param \Plumrocket\SocialLoginFree\Model\Network\CreateNetworkAccountModel $createNetworkAccountModel
     */
    public function __construct(
        UrlHelper $urlHelper,
        Encryptor $encryptor,
        Curl $client,
        Network $networkConfig,
        SessionManagerInterface $customerSession,
        SerializerInterface $serializer,
        RequestInterface $request,
        CreateNetworkAccountModel $createNetworkAccountModel
    ) {
        $this->urlHelper = $urlHelper;
        $this->encryptor = $encryptor;
        $this->client = $client;
        $this->networkConfig = $networkConfig;
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
        $this->request = $request;
        $this->createNetworkAccountModel = $createNetworkAccountModel;
    }

    /**
     * @inheritDoc
     */
    public function getNetworkAccount(array $networkResponse): NetworkAccountInterface
    {
        if (!empty($networkResponse['denied'])) {
            throw new UserAccessDeniedException(__("Access denied was canceled by user"));
        }

        $userData = $this->getData(
            $this->request->getParam('oauth_token', ''),
            $this->request->getParam('oauth_verifier', '')
        );

        return $this->createNetworkAccountModel->execute(
            TwitterComposite::CODE,
            $userData,
            [
                'user_id' => 'id',
                'firstname' => 'name',
                'lastname' => 'name2',
                'email' => 'email',
                'photo' => 'profile_image_url',
            ]
        );
    }

    /**
     * Get user data from network
     *
     * @param string $oAuthToken
     * @param string $oAuthVerifier
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getData(string $oAuthToken, string $oAuthVerifier): array
    {
        $oauth_token_secret = $this->customerSession->getData('oauth_token_secret');
        $applicationId = $this->networkConfig->getApplicationId(TwitterComposite::CODE);
        $oauth_nonce = $this->encryptor->hash(uniqid((string) rand(), true), Encryptor::HASH_VERSION_MD5);
        $applicationSecretKey = $this->networkConfig->getApplicationSecretKey(TwitterComposite::CODE);
        $oauth_timestamp = time();

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::ACCESS_TOKEN_URL) . "&";
        $oauth_base_text .= urlencode("oauth_consumer_key=" . $applicationId . "&");
        $oauth_base_text .= urlencode("oauth_nonce=" . $oauth_nonce . "&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_token=" . $oAuthToken . "&");
        $oauth_base_text .= urlencode("oauth_timestamp=" . $oauth_timestamp . "&");
        $oauth_base_text .= urlencode("oauth_verifier=" . $oAuthVerifier . "&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $applicationSecretKey . '&' . $oauth_token_secret;
        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

        $urlParams = [
            'oauth_nonce' => $oauth_nonce,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $oauth_timestamp,
            'oauth_consumer_key' => $applicationId,
            'oauth_token' => urlencode($oAuthToken),
            'oauth_verifier' => urlencode($oAuthVerifier),
            'oauth_signature' => urlencode($oauth_signature),
            'oauth_version' => '1.0',
        ];

        $this->client->reset();
        $this->client->get(self::ACCESS_TOKEN_URL, $urlParams);
        if (! $body = $this->client->getBody()) {
            throw new LocalizedException(__('Cannot get token.'));
        }

        $result = [];
        parse_str($body, $result);

        if (! $result['oauth_token_secret'] || ! $result['oauth_token']) {
            throw new LocalizedException(__('Cannot get token.'));
        }
        $oauth_nonce = $this->encryptor->hash(uniqid((string) rand(), true), Encryptor::HASH_VERSION_MD5);
        $oauth_timestamp = time();

        $oauth_token = $result['oauth_token'];
        $oauth_token_secret = $result['oauth_token_secret'];
        $screen_name = $result['screen_name'];

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::ACCOUNT_DATA_URL) . '&';
        $oauth_base_text .= urlencode("include_email=true&");
        $oauth_base_text .= urlencode('oauth_consumer_key=' . $applicationId . '&');
        $oauth_base_text .= urlencode('oauth_nonce=' . $oauth_nonce . '&');
        $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
        $oauth_base_text .= urlencode('oauth_timestamp=' . $oauth_timestamp . "&");
        $oauth_base_text .= urlencode('oauth_token=' . $oauth_token . "&");
        $oauth_base_text .= urlencode('oauth_version=1.0&');
        $oauth_base_text .= urlencode('screen_name=' . $screen_name);

        $key = $applicationSecretKey .'&' . $oauth_token_secret;
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $urlParams = [
            'include_email' => 'true',
            'oauth_consumer_key' => $applicationId,
            'oauth_nonce' => $oauth_nonce,
            'oauth_signature' => urlencode($signature),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $oauth_timestamp,
            'oauth_token' => urlencode($oauth_token),
            'oauth_version' => '1.0',
            'screen_name' => $screen_name,
        ];

        $this->client->reset();
        $this->client->get($this->urlHelper->addRequestParam(self::ACCOUNT_DATA_URL, $urlParams));
        if (! $body = $this->client->getBody()) {
            throw new LocalizedException(__('Cannot get token.'));
        }

        $data = $this->serializer->unserialize($body);
        if (empty($data['id'])) {
            throw new LocalizedException(__('Cannot retrieve user profile.'));
        }

        if (! empty($data['name'])) {
            $nameParts = explode(' ', $data['name'], 2);
            $data['name'] = $nameParts[0];
            $data['name2'] = ! empty($nameParts[1]) ? $nameParts[1] : '';
        }

        return $data;
    }
}
