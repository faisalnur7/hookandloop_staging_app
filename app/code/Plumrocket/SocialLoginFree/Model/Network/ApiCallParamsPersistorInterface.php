<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network;

/**
 * Save information from network between different requests
 */
interface ApiCallParamsPersistorInterface
{

    /**
     * Add data to session.
     *
     * @param string $key
     * @param mixed  $value
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function add(string $key, $value): ApiCallParamsPersistorInterface;

    /**
     * Set/replace data with new array.
     *
     * @param array $value
     * @return $this
     */
    public function set(array $value): ApiCallParamsPersistorInterface;

    /**
     * Get data from session.
     *
     * @param string|null $key
     * @return mixed|null
     */
    public function get(string $key = null);

    /**
     * Add guest quote id to preserve it and use before login.
     *
     * This is fix for some networks that lose sessions during login.
     *
     * @param int $quoteId
     * @return \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    public function addQuoteId(int $quoteId): ApiCallParamsPersistorInterface;

    /**
     * Get saved quote id.
     *
     * @return int
     */
    public function getQuoteId(): int;

    /**
     * Clear saved data.
     *
     * @return $this
     */
    public function clear(): ApiCallParamsPersistorInterface;
}
