<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api;

/**
 * @since 2.0.0
 */
interface ConsentLocationTypeInterface
{
    public const TYPE_DEFAULT = 0;
    public const TYPE_PLUMROCKET = 1;
    public const TYPE_CUSTOM = 2;

    /**
     * Retrieve list of consent location types
     *
     * @return array
     */
    public function getList() : array;

    /**
     * Retrieve list of consent location types in label => value format
     *
     * @return array
     */
    public function toOptionArray() : array;

    /**
     * Check if location type is system.
     *
     * @param int $type
     * @return bool
     */
    public function isSystem(int $type) : bool;
}
