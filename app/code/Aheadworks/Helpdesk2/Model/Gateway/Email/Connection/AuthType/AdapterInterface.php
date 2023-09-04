<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * OAuth adapter interface
 */
interface AdapterInterface
{
    /**
     * Create access token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return mixed
     */
    public function createAccessToken(GatewayDataInterface $gateway, ?string $code = null);

    /**
     * Get email by token
     *
     * @param GatewayDataInterface $gateway
     * @param object|array $accessToken
     * @return string
     */
    public function getEmailByToken(GatewayDataInterface $gateway, $accessToken): string;

    /**
     * Convert token to array
     *
     * @param object|array $accessToken
     * @return array
     */
    public function convertTokenToArray($accessToken): array;

    /**
     * Check if access token is expired
     *
     * @param object|array $accessToken $token
     * @return bool
     */
    public function isAccessTokenExpired($accessToken): bool;
}
