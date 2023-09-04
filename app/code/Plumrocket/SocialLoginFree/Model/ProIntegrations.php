<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Xml\Parser;

/**
 * @since 4.0.0
 */
class ProIntegrations
{
    public const CACHE_IDENTIFIER = 'PR_SOCIAL_LOGIN_INTEGRATIONS';

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    private $httpsDriver;

    /**
     * @var array[]
     */
    private $integrations;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $xmlParser;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Load constructor.
     *
     * @param \Magento\Framework\Filesystem\DriverInterface    $httpsDriver
     * @param \Magento\Framework\Xml\Parser                    $xmlParser
     * @param \Magento\Framework\App\CacheInterface            $cache
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        DriverInterface $httpsDriver,
        Parser $xmlParser,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->httpsDriver = $httpsDriver;
        $this->xmlParser = $xmlParser;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * Get integrations.
     *
     * @return array|array[]|string
     */
    public function execute()
    {
        try {
            return $this->getFromCache();
        } catch (NotFoundException $e) {
            if (null === $this->integrations) {
                $this->integrations = [];
                try {
                    if ($fileContent = $this->readInternalFile()) {
                        $parsedXml = $this->xmlParser->loadXML($fileContent)->xmlToArray();
                        if (isset($parsedXml['integrations'])) {
                            $this->integrations = $parsedXml['integrations'];
                        }
                    }
                } catch (LocalizedException $e) {
                    $this->integrations = [];
                }
                $this->saveToCache($this->integrations);
            }
        }

        return $this->integrations;
    }

    /**
     * Load integrations from store.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function readInternalFile() : string
    {
        try {
            $fileContent = $this->httpsDriver->fileGetContents(
                'plumrocket.com/media/info/social_login_integrations.xml',
                null,
                // Disable verifying because:
                // - some clients have problems with ssl
                // - it's not important information
                // - and we can show it in secure way
                stream_context_create(
                    [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ]
                )
            );
        } catch (FileSystemException $e) {
            throw new NotFoundException(__('Fail to fetch latest integrations of Social Login Pro.'));
        }
        return $fileContent;
    }

    /**
     * Get integrations from cache.
     *
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function getFromCache(): array
    {
        if ($moduleVersionsJson = $this->cache->load(self::CACHE_IDENTIFIER)) {
            return $this->serializer->unserialize($moduleVersionsJson);
        }
        throw new NotFoundException(__('Social Login Pro integrations cache not found'));
    }

    /**
     * Cache integrations to minimize requests to store.
     *
     * @param array $integrations
     */
    private function saveToCache(array $integrations): void
    {
        $this->cache->save(
            $this->serializer->serialize($integrations),
            self::CACHE_IDENTIFIER,
            [Config::CACHE_TAG]
        );
    }
}
