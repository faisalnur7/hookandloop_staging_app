<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Common;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pronko\Bing\Api\ConfigInterface;

class Config implements ConfigInterface
{
    /**#@+
     * Bing XML paths
     */
    const XML_PATH_ACTIVE = 'pronko_bing/general/active';
    const XML_PATH_TAG_ID = 'pronko_bing/general/tag_id';
    const XML_PATH_CONVERT_TO_CURRENCY = 'pronko_bing/general/convert_to_currency';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $store
     * @return string
     */
    public function getTagId($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TAG_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int $store
     * @return string
     */
    public function getConvertToCurrency($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONVERT_TO_CURRENCY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int $store
     * @return bool
     */
    public function isActive($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
