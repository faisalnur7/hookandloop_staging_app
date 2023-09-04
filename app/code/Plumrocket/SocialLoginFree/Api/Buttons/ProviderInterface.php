<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api\Buttons;

/**
 * @deprecated since 4.0.0
 * @see \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
 */
interface ProviderInterface
{
    /**
     * @param bool $onlyEnabled
     * @param null $storeId
     * @param bool $forceReload
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     */
    public function getButtons($onlyEnabled = true, $storeId = null, $forceReload = false): array;

    /**
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     */
    public function getPreparedButtons(): array;
}
