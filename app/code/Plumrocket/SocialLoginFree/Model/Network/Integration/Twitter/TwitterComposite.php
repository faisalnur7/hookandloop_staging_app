<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter;

use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\Connector;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\Connector as ConnectorV2;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\UrlResolver;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\UrlResolver as UrlResolverV2;
use Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverInterface;

/**
 * @since 4.0.0
 */
class TwitterComposite implements UrlResolverInterface, NetworkConnectorInterface
{
    public const OAUTH_V2 = 1;

    public const CODE = 'twitter';

    /**
     * @var int
     */
    private $apiVersion;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\UrlResolver
     */
    private $twitterUrlResolverV2;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\UrlResolver
     */
    private $twitterUrlResolverV1;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\Connector
     */
    private $twitterConnectorV2;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\Connector
     */
    private $twitterConnectorV1;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                            $networkConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\UrlResolver $twitterUrlResolverV2
     * @param \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\UrlResolver $twitterUrlResolverV1
     * @param \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2\Connector   $twitterConnectorV2
     * @param \Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V1\Connector   $twitterConnectorV1
     */
    public function __construct(
        Network $networkConfig,
        UrlResolverV2 $twitterUrlResolverV2,
        UrlResolver $twitterUrlResolverV1,
        ConnectorV2 $twitterConnectorV2,
        Connector $twitterConnectorV1
    ) {
        $this->apiVersion = (int) $networkConfig->getNetworkConfig(self::CODE, 'api_version');
        $this->twitterUrlResolverV2 = $twitterUrlResolverV2;
        $this->twitterUrlResolverV1 = $twitterUrlResolverV1;
        $this->twitterConnectorV2 = $twitterConnectorV2;
        $this->twitterConnectorV1 = $twitterConnectorV1;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        if ($this->isOAuthV2()) {
            return $this->twitterUrlResolverV2->getUrl();
        }

        return $this->twitterUrlResolverV1->getUrl();
    }

    /**
     * @inheritDoc
     */
    public function getNetworkAccount(array $networkResponse): NetworkAccountInterface
    {
        if ($this->isOAuthV2()) {
            return $this->twitterConnectorV2->getNetworkAccount($networkResponse);
        }

        return $this->twitterConnectorV1->getNetworkAccount($networkResponse);
    }

    /**
     * Check if oAuth twitter use API V2
     *
     * @return bool
     */
    private function isOAuthV2(): bool
    {
        return $this->apiVersion === self::OAUTH_V2;
    }
}
