<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Amazon\Dictionary;

/**
 * Class \Ess\M2ePro\Model\ResourceModel\Amazon\Dictionary\Specific
 */
class Specific extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\AbstractModel
{
    //########################################

    protected function _construct()
    {
        $this->_init('m2epro_amazon_dictionary_specific', 'id');
    }

    //########################################
}
