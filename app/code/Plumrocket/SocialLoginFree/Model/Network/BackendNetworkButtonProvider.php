<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network;

use Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface;
use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;
use Plumrocket\SocialLoginFree\Api\TypesProviderInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Button;
use Plumrocket\SocialLoginFree\Model\Buttons\Preparer;

class BackendNetworkButtonProvider implements NetworkButtonProviderInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface
     */
    private $buttonResolver;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\TypesProviderInterface
     */
    private $typesProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Buttons\Preparer
     */
    private $preparer;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Button
     */
    private $buttonConfig;

    /**
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface $buttonResolver
     * @param \Plumrocket\SocialLoginFree\Api\TypesProviderInterface         $typesProvider
     * @param \Plumrocket\SocialLoginFree\Model\Buttons\Preparer             $preparer
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Button               $buttonConfig
     */
    public function __construct(
        NetworkButtonResolverInterface $buttonResolver,
        TypesProviderInterface $typesProvider,
        Preparer $preparer,
        Button $buttonConfig
    ) {
        $this->buttonResolver = $buttonResolver;
        $this->typesProvider = $typesProvider;
        $this->preparer = $preparer;
        $this->buttonConfig = $buttonConfig;
    }

    /**
     * @inheritdoc
     */
    public function get(string $networkCode): ButtonInterface
    {
        return $this->buttonResolver->resolve($networkCode);
    }

    /**
     * @inheritdoc
     */
    public function hasButtons($storeId = null): bool
    {
        return (bool) $this->typesProvider->getEnabledList($storeId);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultList($storeId = null): array
    {
        $buttons = [];
        foreach ($this->typesProvider->getEnabledList($storeId) as $type) {
            $buttons[$type] = $this->get($type);
        }

        return $this->preparer->prepareSortAndVisibility(
            $buttons,
            $this->buttonConfig->getSorting($storeId)
        );
    }
}
