<?php

namespace Exinent\CustomStrap\Model;

use Magento\Framework\Model\AbstractModel;

class Customstrap extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Exinent\CustomStrap\Model\ResourceModel\Customstrap');
    }
}