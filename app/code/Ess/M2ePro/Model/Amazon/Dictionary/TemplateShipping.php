<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Amazon\Dictionary;

class TemplateShipping extends \Ess\M2ePro\Model\ActiveRecord\AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Ess\M2ePro\Model\ResourceModel\Amazon\Dictionary\TemplateShipping::class);
    }
}
