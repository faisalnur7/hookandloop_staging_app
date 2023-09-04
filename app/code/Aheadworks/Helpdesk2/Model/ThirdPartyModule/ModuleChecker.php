<?php
namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class ModuleChecker
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule
 */
class ModuleChecker
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * ModuleChecker constructor.
     * @param ModuleListInterface $moduleList
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ModuleListInterface $moduleList, ProductMetadataInterface $productMetadata)
    {
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Check if Aheadworks Coupon Code Generator module enabled
     *
     * @return bool
     */
    public function isAwCouponCodeGeneratorEnabled()
    {
        return $this->moduleList->has('Aheadworks_Coupongenerator');
    }

    /**
     * Check if Aheadworks Customer Attributes module enabled
     *
     * @return bool
     */
    public function isAwCustomerAttributesEnabled()
    {
        return $this->moduleList->has('Aheadworks_CustomerAttributes');
    }

    /**
     * Check if Help Desk module enabled
     *
     * @return bool
     */
    public function isAwHelpDesk1Enabled()
    {
        return $this->moduleList->has('Aheadworks_Helpdesk');
    }

    /**
     * Is new version magento
     *
     * @return bool
     */
    public function isNewVersionMagento() {
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.4.3', '>=')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if Magento Customer Custom Attributes module enabled
     *
     * @return bool
     */
    public function isCustomerCustomAttributesEnabled(): bool
    {
        return $this->moduleList->has('Magento_CustomerCustomAttributes');
    }
}
