<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Ebay;

class Account extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\Component\Child\AbstractModel
{
    /** @var bool */
    protected $_isPkAutoIncrement = false;

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('m2epro_ebay_account', 'account_id');
        $this->_isPkAutoIncrement = false;
    }
}
