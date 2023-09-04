<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Ebay\Template\Shipping;

class Collection extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Ess\M2ePro\Model\Ebay\Template\Shipping::class,
            \Ess\M2ePro\Model\ResourceModel\Ebay\Template\Shipping::class
        );
    }

    /**
     * @param int|string $accountId
     *
     * @return $this
     */
    public function applyLinkedAccountFilter($accountId): Collection
    {
        $this
            ->getSelect()
            ->where('local_shipping_rate_table LIKE ?', "%\"$accountId\":%")
            ->orWhere('international_shipping_rate_table LIKE ?', "%\"$accountId\":%");

        return $this;
    }
}
