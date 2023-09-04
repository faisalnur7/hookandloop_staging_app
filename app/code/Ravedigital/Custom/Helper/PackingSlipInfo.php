<?php
namespace Ravedigital\Custom\Helper;

class PackingSlipInfo extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_resources;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $connection
    ) {
        $this->_resources = $connection;
        parent::__construct($context);
    }

    public function getPackageInfo($package_id){
        $connection= $this->_resources->getConnection();
        $themeTable = $this->_resources->getTableName('shipperhq_order_package_items');
        $sql = "select * from " . $themeTable . " where package_id=".$package_id;
        $result = $connection->fetchAll($sql);
        return $result;
    }
}
