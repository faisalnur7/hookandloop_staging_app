<?php

namespace Exinent\CustomStrap\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Customstrap extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('custom_strap_product', 'entity_id'); //custom_strap_product is table of module
    }
}