<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Helper\Magento;

class Stock
{
    /** @var \Magento\CatalogInventory\Api\StockConfigurationInterface */
    private $stockConfiguration;

    /**
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockConfiguration = $stockConfiguration;
    }

    // ----------------------------------------

    /**
     * Multi Stock is not supported by core Magento functionality.
     * But by changing this method the M2e Pro can be made compatible with a custom solution
     *
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $store
     * @return int
     */
    public function getStockId($store)
    {
        return \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
    }

    /**
     * Multi Stock is not supported by core Magento functionality.
     * But by changing this method the M2e Pro can be made compatible with a custom solution
     *
     * vendor/magento/module-catalog-inventory/Model/StockManagement.php::registerProductsSale()
     *
     * @param null|string|bool|int|\Magento\Store\Api\Data\StoreInterface $store
     * @return int
     */
    public function getWebsiteId($store)
    {
        //if ($store) {
        //    if ($website = $this->getHelper('Magento\Store')->getWebsite($store)) {
        //        return $website->getId();
        //    }
        //}

        return $this->stockConfiguration->getDefaultScopeId();
    }

    /**
     * @return bool
     */
    public function canSubtractQty()
    {
        return $this->stockConfiguration->canSubtractQty();
    }
}
