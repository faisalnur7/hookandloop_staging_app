<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\ResourceModel\Synchronization;

class Log extends \Ess\M2ePro\Model\ResourceModel\Log\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init('m2epro_synchronization_log', 'id');
    }

    /**
     * @return string
     */
    public function getConfigGroupSuffix(): string
    {
        return 'synchronization';
    }
}
