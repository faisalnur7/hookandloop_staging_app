<?php
/**
 * mc-magento2 Magento Component
 *
 * @category Ebizmarts
 * @package mc-magento2
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 10/21/16 4:55 PM
 * @file: MailChimpErrors.php
 */

namespace Ebizmarts\MailChimp\Model;

class MailChimpErrors extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Ebizmarts\MailChimp\Model\ResourceModel\MailChimpErrors::class);
    }
    public function getByStoreIdType($storeId, $id, $type)
    {
        $this->getResource()->getByStoreIdType($this, $storeId, $id, $type);
        return $this;
    }
    public function deleteByStorePeriod($storeId, $interval, $limit)
    {
        return $this->getResource()->deleteByStorePeriod($this, $storeId, $interval, $limit);
    }
}
