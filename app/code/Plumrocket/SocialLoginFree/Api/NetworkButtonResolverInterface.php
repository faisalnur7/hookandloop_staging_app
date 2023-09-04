<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api;

use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;

interface NetworkButtonResolverInterface
{
    /**
     * Get button data by network code
     *
     * @param string $networkCode
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface
     */
    public function resolve(string $networkCode): ButtonInterface;

    /**
     * Resolve login url.
     *
     * @param string $networkCode
     * @param array  $params
     * @return string
     */
    public function resolveLoginUrl(string $networkCode, array $params = []): string;

    /**
     * Resolve registration url.
     *
     * @param string $networkCode
     * @param array  $params
     * @return string
     */
    public function resolveRegistrationUrl(string $networkCode, array $params = []): string;
}
