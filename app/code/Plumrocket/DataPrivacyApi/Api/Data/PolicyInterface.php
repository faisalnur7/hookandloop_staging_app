<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 2.0.0
 */
interface PolicyInterface
{

    public const ID = 'id';
    public const TITLE = 'title';
    public const URL_KEY = 'url_key';
    public const CONTENT = 'content';
    public const VERSION = 'version';

    /**
     * Get id.
     *
     * @return string|int|null
     */
    public function getId();

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get url key.
     *
     * @return string
     */
    public function getUrlKey(): string;

    /**
     * Get version.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Set id.
     *
     * @param int|string|null $id
     * @return mixed
     */
    public function setId($id);

    /**
     * Set title.
     *
     * @param string $title
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setTitle(string $title): PolicyInterface;

    /**
     * Set url key.
     *
     * @param string $urlKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setUrlKey(string $urlKey): PolicyInterface;

    /**
     * Set version.
     *
     * @param string $version
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setVersion(string $version): PolicyInterface;

    /**
     * Set content.
     *
     * @param string $content
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface
     */
    public function setContent(string $content): PolicyInterface;
}
