<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Wizard;

/**
 * Class \Ess\M2ePro\Model\ResourceModel\Wizard\Collection
 */
class Collection extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    //########################################

    protected function _construct()
    {
        $this->_init(
            \Ess\M2ePro\Model\Wizard::class,
            \Ess\M2ePro\Model\ResourceModel\Wizard::class
        );
    }

    //########################################
}
