<?php

namespace Ess\M2ePro\Setup\Update\y21_m01;

use Ess\M2ePro\Model\Setup\Upgrade\Entity\AbstractFeature;

/**
 * Class \Ess\M2ePro\Setup\Update\y21_m01\WalmartCancelRefundOption
 */
class WalmartCancelRefundOption extends AbstractFeature
{
    //########################################

    public function execute()
    {
        $query = $this->getConnection()
            ->select()
            ->from($this->getFullTableName('walmart_account'))
            ->query();

        while ($row = $query->fetch()) {
            $data = \Ess\M2ePro\Helper\Json::decode($row['magento_orders_settings']);

            $data['refund_and_cancellation']['refund_mode'] = '1';

            $this->getConnection()->update(
                $this->getFullTableName('walmart_account'),
                ['magento_orders_settings' => \Ess\M2ePro\Helper\Json::encode($data)],
                ['account_id = ?' => $row['account_id']]
            );
        }
    }

    //########################################
}
