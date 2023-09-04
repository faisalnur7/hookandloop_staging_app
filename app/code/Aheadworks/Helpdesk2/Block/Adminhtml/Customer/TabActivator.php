<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Customer;

use Magento\Backend\Block\Template;

/**
 * Class TabActivator
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Customer
 */
class TabActivator extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_Helpdesk2::customer/tab-activator.phtml';

    /**
     * Check is active
     *
     * @return bool
     */
    public function isActive()
    {
        $param = $this->_request->getParam($this->getParamToTrigger());
        if ($param == $this->getParamValue()) {
            return true;
        }

        return false;
    }
}
