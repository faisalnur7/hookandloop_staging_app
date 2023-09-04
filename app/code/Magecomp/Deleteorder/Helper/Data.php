<?php
namespace Magecomp\Deleteorder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;
    protected $_modelOrderFactory;

    public function __construct(
        Context $context,
        CollectionFactory $modelOrderFactory
    ) {
        parent::__construct($context);
        $this->_modelOrderFactory = $modelOrderFactory;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('deleteorder/general/enable', ScopeInterface::SCOPE_STORE);
    }

    public function getEntityid()
    {
        $data=$this->_modelOrderFactory->create()->getColumnValues('entity_id');
        return $data;
    }

    public function getOrderButtonLabel()
    {
        return $this->scopeConfig->getValue('deleteorder/general/btnheading', ScopeInterface::SCOPE_STORE);
    }
}
