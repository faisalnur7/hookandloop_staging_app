<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Framework\ObjectManagerInterface;
use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network as NetworkConfig;

/**
 * @since 4.0.0
 */
class ButtonResolver implements NetworkButtonResolverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\DefaultButtonsResolver
     */
    private $defaultButtonsResolver;

    /**
     * @param \Magento\Framework\ObjectManagerInterface                        $objectManager
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                $networkConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\DefaultButtonsResolver $defaultButtonsResolver
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        NetworkConfig $networkConfig,
        DefaultButtonsResolver $defaultButtonsResolver
    ) {
        $this->objectManager = $objectManager;
        $this->networkConfig = $networkConfig;
        $this->defaultButtonsResolver = $defaultButtonsResolver;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $networkCode): ButtonInterface
    {
        $buttonResolverClassName = $this->networkConfig->getButtonResolverClass($networkCode);

        if (! empty($buttonResolverClassName)) {
            /** @var \Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface $buttonResolver */
            $buttonResolver = $this->objectManager->get($buttonResolverClassName);
            return $buttonResolver->resolve($networkCode);
        }

        return $this->defaultButtonsResolver->resolve($networkCode);
    }

    /**
     * @inheritDoc
     */
    public function resolveLoginUrl(string $networkCode, array $params = []): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function resolveRegistrationUrl(string $networkCode, array $params = []): string
    {
        return '';
    }
}
