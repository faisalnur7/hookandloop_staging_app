<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\V2;

use Magento\Framework\Url\Helper\Data as UrlHelper;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\TwitterComposite;
use Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverInterface;
use Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver;

/**
 * @since 4.0.0
 */
class UrlResolver implements UrlResolverInterface
{
    private const AUTHORIZE_URL_V2 = 'https://twitter.com/i/oauth2/authorize';

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
     */
    private $modalCallbackUrlResolver;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    private $urlHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver $modalCallbackUrlResolver
     * @param \Magento\Framework\Url\Helper\Data                                 $urlHelper
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                  $networkConfig
     */
    public function __construct(
        ModalCallbackUrlResolver $modalCallbackUrlResolver,
        UrlHelper $urlHelper,
        Network $networkConfig
    ) {
        $this->modalCallbackUrlResolver = $modalCallbackUrlResolver;
        $this->urlHelper = $urlHelper;
        $this->networkConfig = $networkConfig;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $urlParams = [
            'response_type' => 'code',
            'client_id' => $this->networkConfig->getNetworkConfig(TwitterComposite::CODE, 'client_id'),
            'redirect_uri' => $this->modalCallbackUrlResolver->getUrl(TwitterComposite::CODE),
            'scope' => 'tweet.read users.read offline.access',
            'state' => TwitterComposite::CODE,
            'code_challenge' => TwitterComposite::CODE,
            'code_challenge_method' => 'plain',
        ];

        return $this->urlHelper->addRequestParam(self::AUTHORIZE_URL_V2, $urlParams);
    }
}
