<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Lock\Item;

class Collection extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \Ess\M2ePro\Model\Lock\Item::class,
            \Ess\M2ePro\Model\ResourceModel\Lock\Item::class
        );
    }
}
