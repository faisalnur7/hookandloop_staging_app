<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Setup\Upgrade\v1_3_1__v1_3_2;

use Ess\M2ePro\Model\Setup\Upgrade\Entity\AbstractFeature;

class PriceTypeConverting extends AbstractFeature
{
    //########################################

    public function execute()
    {
        $this->getConfigModifier('module')->insert(
            '/magento/attribute/', 'price_type_converting', '0', '0 - disable, \r\n1 - enable'
        );
    }

    //########################################
}
