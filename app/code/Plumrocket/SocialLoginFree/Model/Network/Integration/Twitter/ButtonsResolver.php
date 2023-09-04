<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter;

use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Network\DefaultButtonsResolver;
/**
 * @since 4.0.0
 */
class ButtonsResolver implements NetworkButtonResolverInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\DefaultButtonsResolver
     */
    private $defaultButtonsResolver;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\Network\DefaultButtonsResolver $defaultButtonsResolver
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                $networkConfig
     */
    public function __construct(
        DefaultButtonsResolver $defaultButtonsResolver,
        Network $networkConfig
    ) {
        $this->defaultButtonsResolver = $defaultButtonsResolver;
        $this->networkConfig = $networkConfig;
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $networkCode): ButtonInterface
    {
        $button = $this->defaultButtonsResolver->resolve(TwitterComposite::CODE);
        $apiVersion = (int) $this->networkConfig->getNetworkConfig(TwitterComposite::CODE, 'api_version');
        if ($apiVersion === TwitterComposite::OAUTH_V2) {
            $button->setOpenOnPage(true);
        }

        return $button;
    }

    /**
     * @inheritDoc
     */
    public function resolveLoginUrl(string $networkCode, array $params = []): string
    {
        return $this->defaultButtonsResolver->resolveLoginUrl(TwitterComposite::CODE);
    }

    /**
     * @inheritDoc
     */
    public function resolveRegistrationUrl(string $networkCode, array $params = []): string
    {
        return $this->defaultButtonsResolver->resolveRegistrationUrl(TwitterComposite::CODE);
    }
}
