<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Setup\Upgrade\v1_3_1__v1_3_2;

use Ess\M2ePro\Model\Setup\Upgrade\Entity\AbstractFeature;

class HealthStatus extends AbstractFeature
{
    //########################################

    public function execute()
    {
        $this->getConfigModifier('module')
             ->getEntity('/cron/task/health_status/', 'interval')
             ->updateValue('3600');
    }

    //########################################
}
