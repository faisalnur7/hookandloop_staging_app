<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Api;

/**
 * @api since 1.0.0
 */
interface ConfigInterface
{
    /**
     * @param int $store
     * @return string
     */
    public function getTagId($store = null);

    /**
     * @param int $store
     * @return string
     */
    public function getConvertToCurrency($store = null);

    /**
     * @param int $store
     * @return bool
     */
    public function isActive($store = null);
}
