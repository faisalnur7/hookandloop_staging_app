<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Microsoft;

use Magento\Backend\Model\UrlInterface;

/**
 * Microsoft oauth config
 */
class Config
{
    const RESOURCE_OWNER_DETAILS_URL = 'https://outlook.office365.com/api/v2.0/me/';

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get redirect uri for microsoft client
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->urlBuilder->getUrl('aw_helpdesk2/gateway_microsoft/verify', ['key' => '']);
    }

    /**
     * Get authorize url
     *
     * @param string $tenantId
     * @return string
     */
    public function getAuthorizeUrl(string $tenantId): string
    {
        return sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/authorize', $tenantId);
    }

    /**
     * Get access token url
     *
     * @param string $tenantId
     * @return string
     */
    public function getAccessTokenUrl(string $tenantId): string
    {
        return sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenantId);
    }

    /**
     * Get resource owner details url
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(): string
    {
        return self::RESOURCE_OWNER_DETAILS_URL;
    }
}
