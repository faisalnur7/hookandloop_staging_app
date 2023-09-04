<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Lib\Http\Client\Curl;
use Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\TwitterComposite;
use Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverInterface;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;

/**
 * @since 4.0.0
 */
class UrlResolver implements UrlResolverInterface
{
    private const AUTHORIZE_URL_V1 = 'https://api.twitter.com/oauth/authorize';
    private const REQUEST_TOKEN_URL_V1 = 'https://api.twitter.com/oauth/request_token';

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

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
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $customerSession;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                  $networkConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver $modalCallbackUrlResolver
     * @param \Magento\Framework\Url\Helper\Data                                 $urlHelper
     * @param \Magento\Framework\Encryption\Encryptor                            $encryptor
     * @param \Plumrocket\SocialLoginFree\Lib\Http\Client\Curl                   $client
     * @param \Magento\Framework\Session\SessionManagerInterface                 $customerSession
     */
    public function __construct(
        Network $networkConfig,
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        UrlHelper $urlHelper,
        Encryptor $encryptor,
        Curl $client,
        SessionManagerInterface $customerSession
    ) {
        $this->networkConfig = $networkConfig;
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->urlHelper = $urlHelper;
        $this->encryptor = $encryptor;
        $this->client = $client;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $requestToken = $this->getRequestTokenV1();
        if (! isset($requestToken['oauth_token'])) {
            throw new NetworkIsNotConfiguredException(
                __(
                    'Application Id or Secret Key is empty. Please check Social Login configuration for '
                    . TwitterComposite::CODE
                    . '.'
                )
            );
        }

        return $this->urlHelper->addRequestParam(
            self::AUTHORIZE_URL_V1,
            ['oauth_token' => $requestToken['oauth_token']]
        );
    }

    /**
     * Get request token for api v1.
     *
     * @return array
     * @throws \Exception
     */
    private function getRequestTokenV1(): array
    {
        $redirectUri = $this->modalCallbackUrlResolver->getUrl(TwitterComposite::CODE);
        $oauth_nonce = $this->encryptor->hash(uniqid((string)random_int(0, 5000), true), Encryptor::HASH_VERSION_MD5);
        $applicationId = $this->networkConfig->getApplicationId(TwitterComposite::CODE);
        $oauth_timestamp = time();

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::REQUEST_TOKEN_URL_V1)."&";
        $oauth_base_text .= urlencode("oauth_callback=".urlencode($redirectUri)."&");
        $oauth_base_text .= urlencode("oauth_consumer_key=".$applicationId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $oauth_signature = base64_encode(
            hash_hmac(
                'sha1',
                $oauth_base_text,
                $this->networkConfig->getApplicationSecretKey(TwitterComposite::CODE)."&",
                true
            )
        );

        $urlParams = [
            'oauth_callback' => urlencode($this->modalCallbackUrlResolver->getUrl(TwitterComposite::CODE)),
            'oauth_consumer_key' => $this->networkConfig->getApplicationId(TwitterComposite::CODE),
            'oauth_nonce' => $oauth_nonce,
            'oauth_signature' => urlencode($oauth_signature),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $oauth_timestamp,
            'oauth_version' => '1.0'
        ];

        $this->client->reset();
        $this->client->get(
            $this->urlHelper->addRequestParam(self::REQUEST_TOKEN_URL_V1, $urlParams)
        );

        $body = $this->client->getBody();

        $result = [];
        if ($body) {
            parse_str($body, $result);
        }

        if (isset($result['oauth_token_secret'])) {
            $this->customerSession->setData('oauth_token_secret', $result['oauth_token_secret']);
        }

        return $result;
    }

}
