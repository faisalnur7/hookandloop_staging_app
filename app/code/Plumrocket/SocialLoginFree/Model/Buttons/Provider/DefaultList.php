<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Buttons\Provider;

use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;

class DefaultList implements \Plumrocket\SocialLoginFree\Api\Buttons\ProviderInterface
{

    /**
     * @var array[]
     */
    private $buttonsCache = [];

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $networkButtonProvider;

    /**
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $networkButtonProvider
     */
    public function __construct(
        NetworkButtonProviderInterface $networkButtonProvider
    ) {
        $this->networkButtonProvider = $networkButtonProvider;
    }

    /**
     * @inheritDoc
     */
    public function getButtons($onlyEnabled = true, $storeId = null, $forceReload = false): array
    {
        $cacheKey = $this->getCacheKey([$onlyEnabled, $storeId]);
        if ($forceReload || ! isset($this->buttonsCache[$cacheKey])) {
            $this->buttonsCache[$cacheKey] = $this->networkButtonProvider->getDefaultList($storeId);
        }
        return $this->buttonsCache[$cacheKey];
    }

    /**
     * @inheritDoc
     */
    public function getPreparedButtons(): array
    {
        return $this->getButtons();
    }

    /**
     * Get key for cache
     *
     * @param array $data
     * @return string
     */
    private function getCacheKey($data)
    {
        $serializeData = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $serializeData[$key] = $value->getId();
            } else {
                $serializeData[$key] = $value;
            }
        }
        $serializeData = json_encode($serializeData);
        return sha1($serializeData);
    }
}
