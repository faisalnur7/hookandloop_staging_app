<?php
namespace Aheadworks\Helpdesk2\Model\Config\Backend;

use Aheadworks\Helpdesk2\Model\Source\Config\Order\Status as OrderStatusSource;
use Magento\CatalogInventory\Model\System\Config\Backend\Minqty;

/**
 * Class OrderStatuses
 *
 * @package Aheadworks\Helpdesk2\Model\Config\Backend
 */
class OrderStatuses extends \Magento\Framework\App\Config\Value
{
    /**
     * Prepare value before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (in_array(OrderStatusSource::ALL, $value)) {
            $this->setValue([OrderStatusSource::ALL]);
        }
        parent::beforeSave();

        return $this;
    }
}
